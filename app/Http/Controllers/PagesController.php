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

            $GHrequestRepo = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/repos');
            $repo_json = $GHrequestRepo->handle();

            $commits_url = str_replace('{/sha}', '', json_decode($repo_json)[2]->commits_url);

            $GHrequestCommits = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commits_url);
            $commits_json = $GHrequestCommits->handle();
            $commit_url = json_decode($commits_json)[0]->url;

            $GHrequestCommit = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commit_url);
            $single_commit_json = $GHrequestCommit->handle();
            $files = json_decode($single_commit_json)->files;
            $commit_json = $single_commit_json;
            $GHrequestOrganization = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/orgs');
            $organization_json = json_decode($GHrequestOrganization->handle());
            dd($organization_json);
        }

        return view('admin.dashboard', get_defined_vars());
    }
}
