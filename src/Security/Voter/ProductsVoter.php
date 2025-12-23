<?php

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use function PHPUnit\Framework\throwException;

class ProductsVoter extends Voter
{
    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private \Symfony\Bundle\SecurityBundle\Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $product): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        return $product instanceof Products;
    }

    protected function voteOnAttribute($attribute, mixed $product, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }


        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }
    private function canEdit(): bool
    {
        // if they can edit, they can delete
        // if ($this->canEdit($product, $user)) {
        //     return true;
        // }
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canDelete(): bool
    {
        // if they can edit, they can delete
        // if ($this->canDelete($product, $user)) {
        //     return true;
        // }
        return $this->security->isGranted('ROLE_ADMIN');
    }
}