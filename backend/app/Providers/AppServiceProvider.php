<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider ;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('manage-job', function (User $user, Job $job) {
            return $user->employer && $job->employer_id === $user->employer->id;
        });

        Gate::define('view-applications', function (User $user, Job $job) {
            return $user->employer && $job->employer_id === $user->employer->id;
        });

        Gate::define('manage-application', function (User $user, Application $application) {
            return $user->employer && $application->job->employer_id === $user->employer->id;
        });
        
        Gate::define('accept-application', function (User $user, Application $application) {
          
            return $user->employer && $application->job->employer_id === $user->employer->id;
        });
        
        Gate::define('reject-application', function (User $user, Application $application) {
           
            return $user->employer && $application->job->employer_id === $user->employer->id;
        });
    }
}
     