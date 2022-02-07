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
    public function dashboard()
    {
        $GHrequest = new GitHubApiRequest('dvrobotic', '', '/user');

        $user_json = json_decode($GHrequest->handle());

        $GHrequestRepo = new GitHubApiRequest('dvrobotic', '', '/user/repos');
        $repo_json = $GHrequestRepo->handle();

        $commits_url = str_replace('{/sha}', '', json_decode($repo_json)[1]->commits_url);

        $GHrequestCommits = new GitHubApiRequest('dvrobotic', '', $commits_url);
        $commits_json = $GHrequestCommits->handle();
        $commit_url = json_decode($commits_json)[0]->url;

        $GHrequestCommit = new GitHubApiRequest('dvrobotic', '', $commit_url);
        $single_commit_json = $GHrequestCommit->handle();
        $files = json_decode($single_commit_json)->files;

        return view('admin.dashboard', compact('files'));
    }
}
