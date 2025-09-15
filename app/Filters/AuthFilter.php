<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Not logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        // Optional: role-based access redirection
        // Example: prevent normal users from accessing admin dashboard
        $uri = service('uri')->getPath();

       if (in_array($session->get('role'), ['dashboard viewer', 'verification user']) 
    && strpos($uri, '/dashboard/user') !== false) {
    return redirect()->to('/dashboard')->with('error', 'Access denied.');
}


        if ($session->get('role') === 'super admin' && strpos($uri, 'dashboard/user') !== false) {
            // Admins can still access user dashboard if needed
            // Otherwise, comment this out
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after the request
    }
}
