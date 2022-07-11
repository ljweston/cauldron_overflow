<?php

namespace App\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends BaseController
{
    /**
     * @Route("/api/me", name="app_user_api_me")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function apiMe()
    {
        // serializer component installed so we can view private user data
        // this->json($this->getUser()) calls json response class which calls: json_encode()
        // now serializer takes over json(data, status code, headers[], context[])
        return $this->json($this->getUser(), 200, [], [
            'groups' => ['user:read']
        ]);
    }
}
