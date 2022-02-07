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

            $GHrequestRepo = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], '/user/repos');
            $repo_json = $GHrequestRepo->handle();

            $commits_url = str_replace('{/sha}', '', json_decode($repo_json)[2]->commits_url);

            $GHrequestCommits = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commits_url);
            $commits_json = $GHrequestCommits->handle();
            $commit_url = json_decode($commits_json)[0]->url;

            $GHrequestCommit = new GitHubApiRequest($request->githubUser['nickname'], $request->githubUser['token'], $commit_url);
            $single_commit_json = $GHrequestCommit->handle();
            $files = json_decode($single_commit_json)->files;
        }

        return view('admin.dashboard', compact('user_json', 'repo_json', 'commit_url', 'commits_json', 'files'));
    }
}
