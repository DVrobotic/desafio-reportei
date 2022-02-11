<?php

namespace App\Http\Controllers;

use App\Jobs\GitHubApiRequest;
use App\Models\PullRequest;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Auth;
use phpDocumentor\Reflection\Types\Collection;

class PagesController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home()
    {
        return view('auth.login');
    }

    /**
     * @throws \Exception
     */
    public function dashboard(Request $request)
    {
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

        //-----------------------------------------------------------//

        //--------------------- pull requests -----------------------//

        //$pullRequests = self::pullRequests($request);

        //-----------------------------------------------------------//

        //------- calculating average pull request merge time -------//

        //$time = self::prAverageTime($request);
//        dd($time);

        //-----------------------------------------------------------//

        //------------------------ teams ---------------------------//

       // $team = self::teams($request);

        //-----------------------------------------------------------//

        //-------------------- calculating prs by dev ------------------------//
        $lowerLimit = (new DateTime("-15 days"));
        $higherLimit = (new DateTime("today"));
        $devsContribution = self::devsContribution(null, $lowerLimit, $higherLimit);
        dd($devsContribution);

        //-----------------------------------------------------------//


        return view('admin.dashboard', get_defined_vars());
    }

    /**
     * @param DateTime $
     * @return \Illuminate\Support\Collection
     */
    public static function devsContribution(?bool $state, DateTime $lowerLimit = null, DateTime  $higherLimit = null){
        $lowerLimit = $lowerLimit ?? (new DateTime("0000-00-00 00:00:00"));
        $higherLimit = $higherLimit ?? (new DateTime("now"));
        $pullQuery = $state != null ? PullRequest::where('open', $state) : PullRequest::where('open', '!=', null);
        $pullRequests = $pullQuery
                        ->whereDate('created_at', '>=', $lowerLimit)
                        ->whereDate('created_at', '<=', $higherLimit)
                        ->orderBy('owner', 'asc')
                        ->get()
                        ->groupBy('owner');

        $totalPulls  = $pullRequests->sum(fn ($items) => $items->count());
        $devsContribution = self::calcTotalPercentage($totalPulls, $pullRequests);
        return collect($devsContribution)->sort();
    }

    public static function calcTotalPercentage($total, $array){
        $percentage = [];
        if($total != 0){
            $cumulValue = 0;
            $prevBaseline = 0;
            foreach($array as $key => $pull){
                $cumulValue += $pull->count()/$total;
                $cumulRounded = round($cumulValue, 6);
                $result = $cumulRounded - $prevBaseline;
                $prevBaseline = $cumulRounded;
                $percentage[$key] = $result*100;
            }
        }
        return $percentage;
    }

    public static function teams(Request $request){
        $team_request = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            'orgs/teste-api-github/teams',
        );

        $team_json = $team_request->handle();
        $members_url = str_replace('{/member}', '', json_decode($team_json)[0]->members_url);

        $members_request = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            $members_url,
        );
        $members_json = $members_request->handle();

        dd(json_decode($members_json));

        return get_defined_vars();
    }

    public static function prAverageTime(Request $request){
        //calculating average prs time to merge
        $pulls = [];

        $index = 0;
        do{
            $pr_request = new GitHubApiRequest
            (
                $request->githubUser['nickname'],
                $request->githubUser['token'],
                '',
            );
            $pulls_json = $pr_request->pulls('reportei', 'reportei', 'all', 'page=' . $index . '&per_page=100&');
            $pulls_array = json_decode($pulls_json);
            if(!empty($pulls_array)){
                $pulls = array_merge($pulls, $pulls_array);
            }
            $index++;
        }while(!empty($pulls_array));

        $totalPrMergeTime = 0;
        foreach($pulls as $pull)
        {
            $created_at = strtotime($pull->created_at);
            $closed_at = strtotime($pull->closed_at);
            $mergeTime = $closed_at - $created_at;
            $owner = $pull->user->login;
            $open = $pull->state == 'open';

            PullRequest::updateOrCreate
            ([
                'created_at' => Carbon::parse($pull->created_at)->format('Y-m-d H:i:s'),
                'closed_at' => Carbon::parse($pull->closed_at)->format('Y-m-d H:i:s'),
                'owner' => $owner,
                'mergeTime' => $mergeTime,
                'open' => $open,
            ]);

            $totalPrMergeTime += $mergeTime;
        }

        $totalPrs = count($pulls);
        $averagePrMergeTime = ($totalPrs != 0) ? ($totalPrMergeTime/$totalPrs) : 0;
        //converting unix to days, hours minutes, seconds
        return self::secondsToTime($averagePrMergeTime);

    }

    public static function traffic(Request $request){
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
        $code_frequency = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/reportei/reportei/stats/code_frequency'
        );


        //$code_frequency_json = $code_frequency->handle();


        //getting contributors and their weekly commit timeline
        //w - Start of the week, given as a Unix timestamp.
        //a - Number of additions
        //d - Number of deletions
        //c - Number of commits
        $contributors = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/laravel/laravel/stats/contributors',
        );

        // $contributos_json = $contributors->handle();

        //gets the participation metric, its purpose is to compare the owner and all members commit activity in the last 52 weeks
        $participation = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/reportei/reportei/stats/participation'
        );

        //$participation_json = $participation->handle();

        //This endpoint will return all community profile metrics, including an overall health score,
        // repository description, the presence of documentation, detected code of conduct, detected license,
        // and the presence of ISSUE_TEMPLATE, PULL_REQUEST_TEMPLATE, README, and CONTRIBUTING files.
        //The health_percentage score is defined as a percentage of how many of these four documents are present:
        // README, CONTRIBUTING, LICENSE, and CODE_OF_CONDUCT. For example,
        // if all four documents are present, then the health_percentage is 100. If only one is present,
        // then the health_percentage is 25.
        //content_reports_enabled is only returned for organization-owned repositories.
        $community = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/DVrobotic/desafio-reportei/community/profile',
        );

        //-----------------------------------------------------------//



        //----------------- Traffic metrics -----------------------//

        // $community_json = $community->handle();

        //Get the top 10 referrers over the last 14 days.
        $traffic_referreers = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/reportei/reportei/traffic/popular/referrers'
        );

        //$traffic_referreers_json = $traffic_referreers->handle();


        //Get the top 10 popular contents over the last 14 days.
        $traffic_paths = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/reportei/reportei/traffic/popular/paths'
        );

        //$traffic_paths_json = $traffic_paths->handle();

        //Get the total number of views and breakdown per day or week for the last 14 days.
        // Timestamps are aligned to UTC midnight of the beginning of the day or week.
        // Week begins on Monday.
        $traffic_views = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/reportei/reportei/traffic/views'
        );

        // $traffic_views_json = $traffic_views->handle();


        $traffic_clones = new GitHubApiRequest
        (
            $request->githubUser['nickname'],
            $request->githubUser['token'],
            '/repos/reportei/reportei/traffic/clones'
        );

        // $traffic_clones_json = $traffic_clones->handle();

        return get_defined_vars();
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

            $comments_request = new GitHubApiRequest
            (
                $request->githubUser['nickname'],
                $request->githubUser['token'],
                $comments_url
            );
            $comments_json = json_decode($comments_request->handle());
            $review_comments_request = new GitHubApiRequest
            (
                $request->githubUser['nickname'],
                $request->githubUser['token'],
                $review_comments_url,
            );
            $review_comments_json = json_decode($review_comments_request->handle());
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
}
