<?php

namespace App\Security\Voter;

use App\Entity\QuestionTag;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class QuestionTagVoter extends Voter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;    
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['REMOVE'])
            && $subject instanceof QuestionTag;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof QuestionTag) {
            throw new \Exception('Wrong type somehow passed');
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'REMOVE':
                return $user === $subject->getQuestion()->getOwner();
        }

        return false;
    }
}
