<?php

namespace AppBundle\Security;

use AppBundle\Entity\Sylius\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Webmozart\Assert\Assert;

class OrderActionsVoter extends Voter
{
    const ACCEPT  = 'accept';
    const REFUSE  = 'refuse';
    const DELAY   = 'delay';
    const FULFILL = 'fulfill';
    const CANCEL  = 'cancel';

    private static $actions = [
        self::ACCEPT,
        self::REFUSE,
        self::DELAY,
        self::FULFILL,
        self::CANCEL,
    ];

    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, self::$actions)) {
            return false;
        }

        if (!$subject instanceof Order) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return false;
        }

        if (!$this->authorizationChecker->isGranted('ROLE_RESTAURANT')) {
            return false;
        }

        return $user->ownsRestaurant($subject->getRestaurant());
    }
}
