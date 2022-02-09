<?php

namespace App\Http\Controllers;

use App\Jobs\GitHubApiRequest;
use Illuminate\Http\Request;
use Auth;

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
    public function dashboard(Request $request)
    {
        //$data = self::general_requests($request);

        //$data2 = self::public_org_files($request);

        // $webhook = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '');
        // $webhook_status = $webhook->webhook($request->githubUser['nickname'], $repo_name);


        return view('admin.dashboard', get_defined_vars());
    }

    /**
     * @param Request $request
     * @return array
     */
    public static function public_org_files(Request $request) : array{
        //this entire process is done without logging in or any user information, its a totally public procedure

        //searching for reportei organization
        $GHreportei = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/orgs/laravel');
        $reportei_json = $GHreportei->handle();

        //getting reportei repos url
        $reporter_repos_url = json_decode($reportei_json)->repos_url;

        //requesting for reportei repos (if the org name is known, you can directly search by this method, using "/orgs/{org/repos"
        $GHreporteiRepos = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $reporter_repos_url);
        $reportei_repos_json = $GHreporteiRepos->handle();

        //chosen repo
        $laraground = json_decode($reportei_repos_json)[0];

        //getting repo commits url
        $commits_url = str_replace('{/sha}', '',  $laraground->commits_url);
        //pagination of results with many instances
        $GHlaragroundCommits = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commits_url . '?per_page=3');
        $commits_json = json_decode($GHlaragroundCommits->handle());

        //the /git/ makes so the response from api doesnt have the files and the patch
        $commit_url = str_replace('/git', '', $commits_json[0]->commit->url);

        //request for single commit since when a repo has a limit of 3k files changes, outside that its necessary to look for each commit individually
        $GhCommit = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commit_url ) ;
        $commit_json = json_decode($GhCommit->handle());
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

            //acessing a repo commits with minimal information
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
}
