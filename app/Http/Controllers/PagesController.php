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
        $files = [];
        if($request->githubUser){
            $GHrequest = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user');

            $user_json = json_decode($GHrequest->handle());
            $organization_url = $user_json->organizations_url;

            //all repos unless its a repo from a org without auth access
            $GHrequestRepo = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/repos');
            $repo_json = $GHrequestRepo->handle();

            $commits_url = str_replace('{/sha}', '', json_decode($repo_json)[2]->commits_url);

            //acessing a repo commits with minimal information
            $GHrequestCommits = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commits_url);
            $commits_json = $GHrequestCommits->handle();
            $commit_url = json_decode($commits_json)[0]->url;

            //acessing a commit file/changes...
            $GHrequestCommit = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commit_url);
            $single_commit_json = $GHrequestCommit->handle();
            $files = json_decode($single_commit_json)->files;
            $commit_json = $single_commit_json;

            //access only authorized user organizations, cant even acess public orgs without auth permission
            $GHrequestOrganization = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/orgs');
            $organizations_json = $GHrequestOrganization->handle();

            //can access public organizations or privates where it was granted
            $GHreportei = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/orgs/reportei');
            $json_reportei = $GHreportei->handle();
            $reportei_repo_url = json_decode($json_reportei)->repos_url;


            $GHreporteiRepos = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $reportei_repo_url);
            $reportei_repos_json = $GHreporteiRepos->handle();

            //search for specific public repo by owner/name doesnt work for org/name
            $GHrepoTest = new GitHubApiRequest('', '', '/search/repositories?q=roctocat/Hello-World', false);
            dd(json_decode($GHrepoTest->handle()));

            //so its possible to get a repo from a org name, but not a repo from an org even its public
        }

        return view('admin.dashboard', get_defined_vars());
    }
}
