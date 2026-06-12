<?php

namespace App\Http\Controllers;

use App\Models\clients\Patient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;

    public function __construct()
    {
        $this->user = new Patient();
    }

    protected function getpatientId()
    {
        if (!session()->has('patientId')) {
            $username = session()->get('username');
            if ($username) {
                $patientId = $this->user->getpatientId($username);
                session()->put('patientId', $patientId);
            }
        }
        return session()->get('patientId');
    }
}
