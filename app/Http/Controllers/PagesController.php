<?php

namespace App\Http\Controllers;

use App\Jobs\GitHubApiRequest;
use App\Models\Commit;
use App\Models\PullRequest;
use Carbon\Carbon;
use Cassandra\Date;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;


class PagesController extends Controller
{

    public function home()
    {
        return view('auth.login');
    }

    public function dashboard(Request $request)
    {

        //****************************** CHARTS *********************************//

        //setting up start date, end date and period with its pace
        $start = (new DateTime("-2 months  11:59:59pm"));
        $end = (new DateTime("now"));
        $pace = new DateInterval('P1D');

        $period = new DatePeriod(
            $start,
            $pace,
            $end
        );

        //dates axis, formatted
        $dateArray = self::configX($period);

        //---------------- plotting commits ------------------//

        $commitData = self::getCommitsPlotted($period);

        //-----------------------------------------------------------//

        //------- calculating average pull request merge time -------//

        $time = self::prAverageTime(['reportei/reportei', 'reportei/generator3'], $start, $end);
        $data = self::plotAxis($time, $period);

        $prsOpenBefore = $time['openBefore']['pulls']->values();
        $prsOpenBeforeMergeTime =
            $prsOpenBefore ->count() > 0 ?
                self::secondsToTimeStr($prsOpenBefore[0]
                    ->getDynamicMergeTime($start->getTimestamp(), $end->getTimestamp())) : 0;

        //-----------------------------------------------------------//

        //-------------------- calculating prs by dev ------------------------//

        $devsContribution = self::devsContribution(null, $start, $end);

        //-----------------------------------------------------------//

        //**********************************************************************************//

        return view('admin.dashboard', get_defined_vars());
    }

    public static function getCommitsPlotted(DatePeriod $period){
        $commitInfo = self::getCommitsInfo($period);

        //keys = devs / value = total commits by dev
        $totalCommitsByDev = $commitInfo['groupByDevs']->map(fn($group) => $group->count());

        //getting the devs that worked in the timeline
        $devs = $commitInfo['groupByDevs']->keys();

        //grouping by the devs on the timeline
        $commitsGroupCount = self::getCommitCountGroup($commitInfo['axis'], $devs);
        return
        [
            "devs" => $devs,
            'commitArray' => $commitInfo['groupByDevs']->toArray(),
            "commitCount" => $commitInfo['axis']->map(fn($commitGroup) => $commitGroup->count())->toArray(),
            "commitsGroupCountValues" => $commitsGroupCount->map(fn ($commitGroup) => $commitGroup->values())->toArray(),
            "commitsGroupCountKeys" => $commitsGroupCount->map(fn ($commitGroup) => $commitGroup->keys())->toArray(),
            "commitsByDev" => $totalCommitsByDev->toArray(),
            "devsCommitActivity" => self::getDevCommitActivity($totalCommitsByDev, $period),
            'devsDatasets' => self::getDevDataset($commitsGroupCount, $devs),
        ];
    }

    public static function getCommitsInfo(DatePeriod $period){

        //setting up dates
        $start = $period->getStartDate();
        $end = $period->getEndDate();

        //getting all commits that are eligible to the timeline
        $commitQuery = Commit::within($start, $end);

        //commits collection
        $commits = $commitQuery->get();

        return
        [
            'axis' =>  self::configYCommits($commits, $period), //plotting commits to respective date
            'groupByDevs' => $commitQuery->get()->groupBy('owner'), //commits grouped
        ];
    }

    public static function getCommitCountGroup($commitsAxis, $devs){
        return $commitsAxis->map
        (
        //iterating through the DateInterval commit groupings
            function($commitsByDate) use ($devs)
            {
                //groupBy of commits ownership
                $group =  ($commitsByDate->groupBy('owner'));

                //iterating to count them and adding devs that weren't on that day to use them on datasets
                return $devs->mapWithKeys(fn($dev) =>
                [
                    $dev => isset($group[$dev]) ? $group[$dev]->count() : 0
                ]);
            }
        );
    }
    public static function getContributorsMetric(Request $request){
        $commitsBydev = collect(self::getCommitContribution($request, 'reportei', 'reportei'));
        $totalWeeks = !empty($commitsBydev) ? collect($commitsBydev[0]->weeks)->count() : 0;
        $commitsByDevData = [
            'daily' => $commitsBydev->mapWithKeys(fn($dev) => [$dev->author->login => $totalWeeks > 0 ? $dev->total/($totalWeeks*7) : 0]),
            'weekly' => $commitsBydev->mapWithKeys(fn($dev) => [$dev->author->login => $totalWeeks > 0 ? $dev->total/$totalWeeks : 0]),
            'total' => $commitsBydev->mapWithKeys(fn($dev) => [$dev->author->login => $dev->total]),
        ];
        return get_defined_vars();
    }

    public static function getDevDataset($commitsGroupCount, $devs): array
    {
        $devsDatasets = [];

        foreach($devs as $dev){
            array_push($devsDatasets,
                [
                    'label' => $dev,
                    'backgroundColor' => "#". dechex(rand(0,10000000)) . "33",  //33 is for 0.2 opacity
                    'borderColor' => '#' . dechex(rand(0,10000000)) . "33",
                    'data' => $commitsGroupCount->pluck($dev),
                ]);
        }

        return $devsDatasets;
    }

    public static function getDevCommitActivity($totalCommitsByDev, DatePeriod $datePeriod) : Collection
    {
        //setting up dates
        $start = $datePeriod->getStartDate();
        $end = $datePeriod->getEndDate();

        //getting the interval in seconds
        $interval = $end->getTimestamp() - $start->getTimestamp();

        if($interval > 0)
        {
            //days, weeks and months in seconds dividing the interval
            $days = $interval/(60*60*24);
            $weeks = $days/7;
            $months = $days/30;

            //making new associative collection to each dev daily, weekly and monthly commit activity
            return $totalCommitsByDev->map
            (fn($value) =>
            [
                'daily' => $value/$days,
                'weekly' => $value/$weeks,
                'monthly' => $value/$months
            ]
            );
        }
        return collect([]);

    }


    public static function getCommitContribution(Request $request, $owner, $repo){
        //getting contributors and their weekly commit timeline
        //w - Start of the week, given as a Unix timestamp.
        //a - Number of additions
        //d - Number of deletions
        //c - Number of commits
        $contributors = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            "/repos/{$owner}/{$repo}/stats/contributors",
        );

        return json_decode($contributors->handle());
    }

    public static function plotAxis($time, DatePeriod $period){

        $start = $period->getStartDate();
        $end = $period->getEndDate();
        //getting the intersection pulls request collection
        $pulls = $time['allPulls']['pulls'];

        //using native function to get time period
        //time period


        //making the pull requests groupings in each day
        //making so both are different datasets and can be displayed uniquely in chart js
        $closedPullsCollection = self::configYforClosing($pulls, $period); #closed prs grouping
        $openPullsCollection = self::configYforOpening($pulls, $period); #open prs grouping


        //returning the sum of the dynamic merge time on the pulls
        //usin
        $mergeClosedDateArray = $closedPullsCollection->map(self::getGroupMergetime($pulls, $start, $end))->toArray();
        $mergeOpenDateArray = $openPullsCollection->map(self::getGroupMergetime($pulls, $start, $end))->toArray();
        $pullsCount =
        [
            'open' => $openPullsCollection->map(fn($pulls) => $pulls->count()),
            'closed' => $closedPullsCollection->map(fn($pulls) => $pulls->count())
        ];
        return compact('mergeClosedDateArray', 'mergeOpenDateArray', 'pullsCount');
    }

    public static function configX($period) : \Illuminate\Support\Collection
    {
        //using map collect function to format all dates
        return collect($period)->map(fn($date) => $date->format('d-m-Y'));
    }

    public static function getFormat(DatePeriod $period){
        if($period->getDateInterval()->y){
            return 'Y';
        } else if($period->getDateInterval()->m){
            return 'm-Y';
        } else{
            return 'd-m-Y';
        }
    }

    public static function configYCommits($commits, DatePeriod $period){
        //using collection map function to filter and compare all dates in period only for closed ones
        //using their closing time as the parameter
        $end = $period->getEndDate();
        $format = self::getFormat($period);
        return collect($period)->map
        (
            fn($date)
            => $commits->filter(fn($commit)
            => date($format, $commit->created_at) == $date->format($format))
        );
    }

    public static function configYforClosing($pulls, DatePeriod $period){
        //using collection map function to filter and compare all dates in period only for closed ones
        //using their closing time as the parameter
        $end = $period->getEndDate();
        $format = self::getFormat($period);
        return collect($period)->map
        (
            fn($date)
            => $pulls->filter(fn($pull)
            => !$pull->isOpen($end) && date($format, $pull->closed_at) == $date->format($format))
        );
    }

    public static function configYforOpening($pulls, $period){
        //using collection map function to filter and compare all dates in period only for open ones
        //using their creation time as the parameter
        $end = $period->getEndDate();
        $format = self::getFormat($period);
        return collect($period)->map
        (
            fn($date)
            => $pulls->filter(fn($pull)
            => $pull->isOpen($end) && $pull->dateMatches($date, $format))
        );
    }


    public static function getGroupMergetime($pulls, $start, $end) : callable{
        //return closure for the average merge time for each day
        return function($pulls) use ($start, $end){
            $total = $pulls->count();
            return $pulls->sum(fn($pull) =>
                $pull->getDynamicMergeTime($start->getTimestamp(), $end->getTimestamp())) / ($total > 0 ? $total : 1);
        };
    }

    public static function devsContribution(?bool $state, DateTime $start = null, DateTime  $end = null){
        //making so the datetime becomes a unix timestamp
        $start = $start != null ? $start->getTimestamp() : strtotime("0000-00-00 00:00:00");
        $end = $end != null ? $end->getTimestamp() : strtotime("now");

        //getting only the prs created in the timeline
        $pullRequests = PullRequest::onlyWithin($start, $end)->get()->groupBy('owner');

        //getting total pulls requests created
        $totalPulls  = $pullRequests->sum(fn ($items) => $items->count());

        //calculating devs contribution, based on prs ownership
        $devsContribution = self::calcTotalPercentage($totalPulls, $pullRequests);
        return
        [
            'contribution' => collect($devsContribution)->sortDesc(),
            'devPrs' => $pullRequests->map(fn($pulls) => $pulls->count()),
            'total' => $totalPulls,
        ];
    }

    //algorith to
    public static function calcTotalPercentage($total, $array)
    {
        $percentage = [];
        if($total != 0){
            $cumulValue = 0;
            $prevBaseline = 0;
            foreach($array as $key => $pull){
                $cumulValue += $pull->count()/$total;
                $cumulRounded = round($cumulValue, 4);
                $result = $cumulRounded - $prevBaseline;
                $prevBaseline = $cumulRounded;
                $percentage[$key] = $result * 100;
            }
        }
        return $percentage;
    }

    public static function teams(Request $request)
    {
        $team_request = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            'orgs/teste-api-github/teams',
        );

        $team_json = $team_request->handle();
        $members_url = str_replace('{/member}', '', json_decode($team_json)[0]->members_url);

        $team_request->changeRequest($members_url);
        $members_json =  $team_request->handle();

        return get_defined_vars();
    }
    public static function saveCommitsToDatabase(Request $request, $owner,$repo)
    {
        $commits = [];
        $index = 0;
        do{
            $pr_request = new GitHubApiRequest
            (
                $request->githubUser['nickname'],
                $request->githubUser['token'],
                "/repos/{$owner}/{$repo}/commits?page={$index}&per_page=100",
            );

            $commits_json = $pr_request->handle();
            $commits_array = json_decode($commits_json);
            if(!empty($commits_array)){
                $commits = array_merge($commits, $commits_array);
            }
            $index++;
        }while(!empty($commits_array));

        foreach($commits as $commit)
        {
            $created_at = strtotime($commit->commit->committer->date);
            $pr_owner = isset($commit->author->login) ? $commit->author->login : '';

            Commit::updateOrCreate
            ([
                'created_at' => $created_at,
                'owner' => $pr_owner,
                'repo' => $owner . '/' . $repo,
            ],
            [
                'created_at' => $created_at,
                'owner' => $pr_owner,
                'repo' => $owner . '/' . $repo,
            ]);
        }
    }

    public static function savePrsToDatabase(Request $request, $owner,$repo)
    {
        $pulls = [];
        $index = 0;
        do{
            $pr_request = new GitHubApiRequest
            (
                $request->githubUser['nickname'],
                $request->githubUser['token'],
                '',
            );
            $pulls_json = $pr_request->pulls($owner, $repo, 'all', 'page=' . $index . '&per_page=100&');
            $pulls_array = json_decode($pulls_json);
            if(!empty($pulls_array)){
                $pulls = array_merge($pulls, $pulls_array);
            }
            $index++;
        }while(!empty($pulls_array));

        foreach($pulls as $pull)
        {
            $created_at = strtotime($pull->created_at);
            $closed_at = strtotime($pull->closed_at);
            $pr_owner = $pull->user->login;
            $mergeTime = $pull->isOpen() ? strtotime('now') - $created_at : $closed_at - $created_at;

            PullRequest::updateOrCreate
            ([
                'created_at' => $created_at,
                'owner' => $pr_owner,
                'repo' => $owner . '/' . $repo,
            ],
            [
                'mergeTime' => $mergeTime,
                'closed_at' => $closed_at,
            ]);
        }
    }

    public static function prAverageTime($repos = [], DateTime $start = null, DateTime  $end = null)
    {
        //calculating average prs time to merge
        $start = $start != null ? $start->getTimestamp() : strtotime("0000-00-00 00:00:00");
        $end = $end != null ? $end->getTimestamp() : strtotime("now");

        //getting all pull requests from the repos
        $repoQuery = empty($repos) ? PullRequest::all() : PullRequest::whereIn('repo', $repos);

        //querying the timelines intersections
        $pullQuery = $repoQuery->intersection($start, $end);

        //getting the collection
        $allPulls = $pullQuery->get();

        //using custom scope to get only closed ones
        $onlyClosed = $pullQuery->open($end, false)->get();

        //getting only the pulls created within the start and end dates,
        $onlyWithin = $allPulls->where('created_at', '>', $start);

        //getting the closed ones and only the ones created within
        $onlyClosedWithin = $onlyClosed->where('created_at', '>', $start);

        //using a filter collection function to remove to get only the nodes open before the timeline as
        //they wont appear on the chart, so its a very useful information and it affects the chart average time
        $openBefore = $allPulls->filter(function($pull) use ($start, $end){
            return ($pull->created_at < $start) && ($pull->open || $pull->closed_at > $end);
        });

        //converting unix to days, hours minutes, seconds
        return
            [
                'allPulls' => self::calcPrAverageTime($allPulls, $start, $end),
                'onlyClosed' => self::calcPrAverageTime($onlyClosed, $start, $end),
                'onlyWithin' => self::calcPrAverageTime($onlyWithin, $start, $end),
                'onlyClosedWithin' => self::calcPrAverageTime($onlyClosedWithin, $start, $end),
                'openBefore' => self::calcPrAverageTime($openBefore, $start, $end),
            ];

    }

    public static function calcPrAverageTime(EloquentCollection $pulls, int $start, int  $end){
        $totalPrMergeTime = $pulls->sum(fn(PullRequest $pull) => $pull->getDynamicMergeTime($start, $end));
        $totalPrs = count($pulls);
        $averagePrMergeTime = ($totalPrs != 0) ? ($totalPrMergeTime/$totalPrs) : 0;
        $format = "Y-m-d H:i:s";
        //converting unix to days, hours minutes, seconds
        return
            [
                'start' => date($format, $start),
                'end' => date($format, $end),
                'times' => self::secondsToTimeStr($averagePrMergeTime), //average merge time
                'informations' => $pulls->map(fn($pull) =>  //outputs: startDate - endDate / mergeTime
                    date($format, $pull->created_at) .
                    ' - ' .
                    ($pull->open ? '?' : date($format, $pull->closed_at)) .
                    ' / mergeTime: ' . $pull->getDynamicMergeTime($start, $end)),
                'pulls' => $pulls, //pull requests model collection
            ];
    }

    public static function traffic(Request $request){

        $traffic_request = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            ''
        );

        //pay attention as the information is cashed and can be delayed, so a queue with error tolerance
        //is advised, as the first our second request might now receive the information immediately,
        // the first request starts the job in the github api, when its done, we can retrieve the information
        //problem is, we cant precisely know that, so we must try with some time spacing to give the api time to process
        // a fail retry system using laravel queues might suffice that

        //Returns a weekly aggregate of the number of additions and deletions pushed to a repository.
        //the response is in the following formatting:
        // 0 => timestamp in unix
        // 1 => number of additions pushed to a repository.
        // 2 => number of deletions pushed to a repository.

        //$code_frequency_json = $traffic_request->handle('/repos/reportei/reportei/stats/code_frequency');


        //getting contributors and their weekly commit timeline
        //w - Start of the week, given as a Unix timestamp.
        //a - Number of additions
        //d - Number of deletions
        //c - Number of commits
        $contributors_json = json_decode($traffic_request->handle('/repos/laravel/laravel/stats/contributors'));



        $participation_json = json_decode($traffic_request->handle('/repos/reportei/reportei/stats/participation'));

        //This endpoint will return all community profile metrics, including an overall health score,
        // repository description, the presence of documentation, detected code of conduct, detected license,
        // and the presence of ISSUE_TEMPLATE, PULL_REQUEST_TEMPLATE, README, and CONTRIBUTING files.
        //The health_percentage score is defined as a percentage of how many of these four documents are present:
        // README, CONTRIBUTING, LICENSE, and CODE_OF_CONDUCT. For example,
        // if all four documents are present, then the health_percentage is 100. If only one is present,
        // then the health_percentage is 25.
        //content_reports_enabled is only returned for organization-owned repositories.

        $community_json = json_decode($traffic_request->handle('/repos/DVrobotic/desafio-reportei/community/profile'));
        //-----------------------------------------------------------//



        //----------------- Traffic metrics -----------------------//

        // $community_json = $community->handle();

        //Get the top 10 referrers over the last 14 days.
        $traffic_referreers_json = json_decode($traffic_request->handle('/repos/reportei/reportei/traffic/popular/referrers'));


        //Get the top 10 popular contents over the last 14 days.
        $traffic_paths_json = json_decode($traffic_request->handle('/repos/reportei/reportei/traffic/popular/paths'));

        //Get the total number of views and breakdown per day or week for the last 14 days.
        // Timestamps are aligned to UTC midnight of the beginning of the day or week.
        // Week begins on Monday.


         $traffic_views_json = json_decode($traffic_request->handle('/repos/reportei/reportei/traffic/views'));


         $traffic_clones_json = json_decode($traffic_request->handle('/repos/reportei/reportei/traffic/clones'));

        return $contributors_json;
    }

    public static function pullRequests(Request $request){
        //List pull requests, can sort, filter by branch, and type (closed or open)
        $pulls = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '',
        );

        $pulls_json = $pulls->pulls('reportei', 'reportei', 'all');

        //search for the first commentary and review commentary
        foreach (json_decode($pulls_json) as $key => $json) {
            $comments_url = $json->comments_url;
            $review_comments_url = $json->review_comments_url;

            $comments_json = json_decode($pulls->handle($comments_url));

            $review_comments_json = json_decode($pulls->handle($review_comments_url));
            if(count($comments_json) != 0 || count($review_comments_json) != 0){
                dd($comments_json, $review_comments_json);
            }
        }

        return get_defined_vars();
    }

    public static function webhookCrud(Request $request){

        $webhook = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/DVrobotic/desafio-reportei/hooks'
        );

        //getting hooks
        $weebhook_json = json_decode($webhook->handle());

        $hook_id = isset($weebhook_json[0]) ? $weebhook_json[0]->id : '1';

        //editing hooks
        $edition = $webhook->patch('DVrobotic', 'desafio-reportei', $hook_id);

        //deleting hooks
        $delete = $webhook->delete('DVrobotic', 'desafio-reportei', $hook_id);

        //-----------------------------------------------------------//

        return get_defined_vars();
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function public_org_files(Request $request) : array{
        //this entire process is done without logging in or any user information, its a totally public procedure

        //searching for reportei organization
        $GHreportei = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/orgs/laravel');
        $reportei_json = json_decode($GHreportei->handle());

        //getting reportei repos url
        $reporter_repos_url = $reportei_json->repos_url;

        //requesting for reportei repos (if the org name is known, you can directly search by this method, using "/orgs/{org/repos"
        $GHreporteiRepos = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $reporter_repos_url);
        $reportei_repos_json = json_decode($GHreporteiRepos->handle());

        //chosen repo
        $laraground = $reportei_repos_json[0];

        //getting repo commits url
        $commits_url = str_replace('{/sha}', '',  $laraground->commits_url);
        //pagination of results with many instances
        $GHlaragroundCommits = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commits_url . '?page=1&per_page=100');
        $commits_json = json_decode($GHlaragroundCommits->handle());

        //the /git/ makes so the response from api doesnt have the files and the patch
        $commit_url = str_replace('/git', '', $commits_json[0]->commit->url);

        //request for single commit since when a repo has a limit of 3k files changes, outside that its necessary to look for each commit individually
        $GhCommit = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commit_url ) ;
        $commit_json = json_decode($GhCommit->handle());
        dd($commit_json);
        return get_defined_vars();
    }


    /**
     * @param Request $request
     * @return array
     */
    public static function general_requests(Request $request) : array
    {
        $files = [];
        if ($request->githubUser) {
            $GHrequest = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user');

            $user_json = json_decode($GHrequest->handle());
            $organization_url = $user_json->organizations_url;

            //all repos unless its a repo from a org without auth access
            $GHrequestRepo = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/repos');
            $repo_json = $GHrequestRepo->handle();

            $commits_url = str_replace('{/sha}', '', json_decode($repo_json)[2]->commits_url);
            $repo_name = json_decode($repo_json)[1]->name;
            //$repo_name = json_decode($repo_json)[2]->repo

            //accessing a repo commits with minimal information
            $GHrequestCommits = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commits_url);
            $commits_json = $GHrequestCommits->handle();
            $commit_url = json_decode($commits_json)[0]->url;

            //acessing a commit file/changes...
            $GHrequestCommit = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commit_url);
            $single_commit_json = $GHrequestCommit->handle();
            $files = json_decode($single_commit_json)->files;
            $commit_json = $single_commit_json;
           // dd(json_decode($commit_json));

            //access only authorized user organizations, cant even acess public orgs without auth permission
            $GHrequestOrganization = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/orgs');
            $organizations_json = $GHrequestOrganization->handle();

            //can access public organizations or privates where it was granted
            $GHreportei = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/orgs/reportei');
            $json_reportei = $GHreportei->handle();

            $reportei_repo_url = json_decode($json_reportei)->repos_url;

            //reportei repos
            $GHreporteiRepos = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $reportei_repo_url);
            $reportei_repos_json = $GHreporteiRepos->handle();

            //its possible to get public repos through search
            $GHrepoTest = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/search/repositories?q=repo:teste-api-github/teste-org', true);

           // $webhook = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '');
           // $webhook_status = $webhook->webhook($request->githubUser['nickname'], $repo_name);
        }
        return get_defined_vars();
    }

    public function payloadHandler(Request $request){
        dd('test');
    }

    public static function secondsToTime($inputSeconds) {

        $secondsInAMinute = 60;
        $secondsInAnHour  = 60 * $secondsInAMinute;
        $secondsInADay    = 24 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        // return the final array
        $obj = array(
            'd' => (int) $days,
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        );
        return $obj;
    }

    public static function secondsToTimeStr($inputSeconds){
        $obj = self::secondsToTime($inputSeconds);
        return "{$obj['d']}D {$obj['h']}H {$obj['m']}M {$obj['s']}S";
    }

    public static function apianalysis(Request $request){
        //----------------- user,search,general requests -----------------------//

        //$general = self::general_requests($request);

        //-----------------------------------------------------------//



        //----------------- organizations requests -----------------------//

        //$organizations = self::public_org_files($request);

        //-----------------------------------------------------------//



        //----------------- webhooks requests -----------------------//

        //$webhooks = self::webhookCrud($request);

        //-----------------------------------------------------------//



        //----------------- general/traffic metrics -----------------------//

        //$metrics = self::traffic($request);
        // dd($metrics);

        //-----------------------------------------------------------//

        //--------------------- pull requests -----------------------//

//        $pullRequests = self::pullRequests($request);
//
//        dd($pullRequests);

        //-----------------------------------------------------------//

        //--------------------- pull requests -----------------------//

        // $pullRequests = self::savePrsToDatabase($request, 'reportei', 'generator3');
//       dd('teste');

        //-----------------------------------------------------------//

        //------------------------ teams ---------------------------//

//        $team = self::teams($request);
//        dd($team);

        //-----------------------------------------------------------//

        //------------ saving commits to db ------------------------//

        //self::saveCommitsToDatabase($request, 'reportei', 'reportei');

        //-----------------------------------------------------------//

        //------------ devs commit contribution metric ---------------//

        //self::getContributorsMetric($request);

        //------------------------------------------------------------//
    }
}
