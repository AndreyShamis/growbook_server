<?php

namespace App\Security\Voter;

use App\Entity\Plant;
use App\Entity\User;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PlantVoter extends Voter
{

    // these strings are just invented: you can use anything
    protected const VIEW = 'view';
    protected const EDIT = 'edit';
    protected const DELETE = 'delete';

    private $decisionManager;

    /**
     * PlantVoter constructor.
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE), true)) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Plant) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     * @throws \LogicException
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $logedin_user = $token->getUser();

        if (!$logedin_user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Plant $plant */
        $plant = $subject;

        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($plant, $logedin_user);
            case self::EDIT:
                return $this->canEdit($plant, $logedin_user);
            case self::DELETE:
                return $this->canDelete($plant, $logedin_user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Plant $thePlant
     * @param User $logedin_user
     * @return bool
     */
    private function canView(Plant $plant, User $logedin_user): bool
    {
        $owners = $plant->getOwners()->toArray();
        /** @var User $owner */
        foreach ($owners as $owner) {
            if ($owner->getId() === $logedin_user->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Plant $plant
     * @param User $logedin_user
     * @return bool
     */
    private function canEdit(Plant $plant, User $logedin_user): bool
    {
        return $this->canView($plant, $logedin_user);
//        // this assumes that the data object has a getOwner() method
//        // to get the entity of the user who owns this data object
//        /** @var PersistentCollection $logedin_user_roles */
//        $logedin_user_roles = $logedin_user->getRoles();
//        return $logedin_user->getId() === $plant->getId()
//            || \in_array('ROLE_ADMIN', (array)$logedin_user_roles, true)
//            || \in_array('ROLE_SUPER_ADMIN', (array)$logedin_user_roles, true);
    }

    /**
     * @param Plant $plant
     * @param User $logedin_user
     * @return bool
     */
    private function canDelete(Plant $plant, User $logedin_user): bool
    {
        return $this->canView($plant, $logedin_user);
//        // this assumes that the data object has a getOwner() method
//        // to get the entity of the user who owns this data object
//        /** @var PersistentCollection $logedin_user_roles */
//        $logedin_user_roles = $logedin_user->getRoles();
//        return \in_array('ROLE_SUPER_ADMIN', (array)$logedin_user_roles, true) && !$plant->isLdapUser();
    }
}
