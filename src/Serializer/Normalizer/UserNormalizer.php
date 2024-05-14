<?php

// This class normalizes and denormalizes the User entity
// Path: src/Serializer/Normalizer/UserNormalizer.php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use App\DTO\UserDTO;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements ContextAwareNormalizerInterface, DenormalizerInterface
{
    private $normalizer;
    private $security;

    public function __construct(
        ObjectNormalizer $normalizer,
        Security $security
    ) {
        $this->normalizer = $normalizer;
        $this->security = $security;
    }

    public function normalize($user, ?string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($user, $format, $context);

        // Here, add, edit, or delete some data:
        $data["company"] = $user->getCompany()->getName();

        return $data;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /** @var User */
        $currentUser = $this->security->getUser();

        $userDTO = new UserDTO(
            $data['email'] ?? null,
            $data['fullname'] ?? null,
            $data['password'] ?? null,
            $currentUser->getCompany()
        );

        return $userDTO;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = [])
    {
        return $data instanceof UserDTO;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $type === UserDTO::class;
    }
}
