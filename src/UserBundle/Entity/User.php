<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 12:26.
 */
namespace UserBundle\Entity;

use AdminBundle\Entity\AdminRole;
use AdminBundle\Entity\OrganizationAdminAccount;
use AdminBundle\Entity\ShopAdminAccount;
use AdminBundle\Entity\SuperAdminAccount;
use AppBundle\Entity\BaseRole;
use AppBundle\Entity\City;
use AppBundle\Entity\TimestampableEntity;
use AppBundle\Exceptions\ConsistencyException;
use ChipBundle\Entity\Chip;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GiftBundle\Entity\Gift;
use JMS\Serializer\Annotation as JMS;
use PromotionBundle\Entity\Promotion;
use PromotionBundle\Entity\UserPromotion;
use SubscriptionBundle\Entity\Notification;
use SubscriptionBundle\Entity\Push;
use SubscriptionBundle\Entity\UserEvent;
use SubscriptionBundle\Entity\UserSubscription;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 *
 * @ORM\Table(name="users__users")
 * @ORM\Entity()
 *
 * @JMS\ExclusionPolicy("all")
 */
class User extends TimestampableEntity implements UserInterface, EquatableInterface
{
    const PUBLIC_CARD = 'user__public';
    const INFO_CARD = 'user__info';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose()
     * @JMS\Groups({User::INFO_CARD, USER::PUBLIC_CARD})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups({User::INFO_CARD, USER::PUBLIC_CARD})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups({User::INFO_CARD, USER::PUBLIC_CARD})
     */
    protected $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups({User::INFO_CARD, USER::PUBLIC_CARD})
     */
    protected $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    protected $birthday;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Phone",
     *     mappedBy="user",
     *     cascade={"remove", "persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @Assert\Count(
     *     min=1,
     *     minMessage="User must have at least one phone"
     * )
     */
    protected $phones;

    /**
     * @var UserPromotion[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="PromotionBundle\Entity\UserPromotion",
     *     mappedBy="user",
     *     cascade={"remove", "persist"},
     *     orphanRemoval=true
     * )
     */
    protected $promotions;

    /**
     * @var ArrayCollection|AccessToken[]
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\AccessToken",
     *     mappedBy="user",
     *     cascade={"remove", "persist"},
     *     orphanRemoval=true
     * )
     */
    protected $accessTokens;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", nullable=true)
     */
    protected $secret;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_code", type="string", nullable=true)
     */
    protected $smsCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sms_code_dt", type="datetime", nullable=true)
     */
    protected $smsCodeDt;

    /**
     * @ORM\ManyToMany(targetEntity="AdminBundle\Entity\AdminRole")
     * @ORM\JoinTable(
     *  name="admin__users_admins_roles",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $adminRoles;

    /**
     * @var SuperAdminAccount
     *
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\SuperAdminAccount", mappedBy="user", cascade={"persist"})
     */
    protected $superAdminAccount;

    /**
     * @var OrganizationAdminAccount
     *
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\OrganizationAdminAccount", mappedBy="user", cascade={"persist"})
     */
    protected $organizationAdminAccount;

    /**
     * @var ShopAdminAccount
     *
     * @ORM\OneToOne(targetEntity="AdminBundle\Entity\ShopAdminAccount", mappedBy="user", cascade={"persist"})
     */
    protected $shopAdminAccount;

    /**
     * @var string|null
     */
    protected $requestToken;

    /**
     * @var UserOrganizationRole[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\UserOrganizationRole",
     *     mappedBy="user",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    protected $organizationRoles;

    /**
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\UserShopRole",
     *     mappedBy="user",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    protected $shopRoles;

    /**
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\UserRole", inversedBy="users")
     * @ORM\JoinTable(
     *  name="users__users_users_roles",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $userRoles;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", inversedBy="users")
     *
     * @JMS\Expose()
     * @JMS\Groups({User::INFO_CARD})
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="ChipBundle\Entity\Chip", mappedBy="user")
     *
     * @ORM\OrderBy({"since" = "ASC"})
     */
    protected $chips;

    /**
     * @var Passport[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Passport",
     *     mappedBy="user",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     * @ORM\OrderBy({"since" = "ASC"})
     */
    protected $passports;

    /**
     * @var Gift[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="GiftBundle\Entity\Gift", mappedBy="giver", orphanRemoval=true)
     */
    protected $gifts;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="GenderType", nullable=true)
     *
     * @JMS\Expose()
     * @JMS\Groups({USER::INFO_CARD, USER::PUBLIC_CARD})
     */
    protected $gender;

    /**
     * @var UserSubscription[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SubscriptionBundle\Entity\UserSubscription", mappedBy="user", orphanRemoval=true)
     */
    protected $subscriptions;

    /**
     * @var Notification[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SubscriptionBundle\Entity\UserNotification", mappedBy="user", orphanRemoval=true)
     */
    protected $notifications;

    /**
     * @var Push[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="SubscriptionBundle\Entity\Push", mappedBy="user", orphanRemoval=true)
     */
    protected $pushes;

    /**
     * @var UserEvent[]|ArrayCollection $userEvents
     *
     * @ORM\OneToMany(targetEntity="SubscriptionBundle\Entity\UserEvent", mappedBy="user", orphanRemoval=true)
     */
    protected $userEvents;

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        return ($user instanceof self) && $user->getId() === $this->getId();
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return BaseRole[]
     */
    public function getRoles()
    {
        $roles = [];

        /**
         * @var UserOrganizationRole $role
         */
        foreach ($this->organizationRoles as $role) {
            $roles[] = $role->getRole();
        }

        /**
         * @var UserShopRole $role
         */
        foreach ($this->shopRoles as $role) {
            $roles[] = $role->getRole();
        }

        /**
         * @var AdminRole $role
         */
        foreach ($this->adminRoles as $role) {
            $roles[] = $role;

            if ($role->getRole() === 'ROLE_ADMIN_SUPER_ADMIN') {
                $roles[] = 'ROLE_SONATA_ADMIN';
            }
        }

        /**
         * @var UserRole $role
         */
        foreach ($this->userRoles as $role) {
            $roles[] = $role;
        }

        return $roles;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        $superAdminAccount = $this->getSuperAdminAccount();
        if ($superAdminAccount) {
            return $superAdminAccount->getPassword();
        }

        $organizationAdminAccount = $this->getOrganizationAdminAccount();
        if ($organizationAdminAccount) {
            return $organizationAdminAccount->getPassword();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @return int
     */
    public function getUsername()
    {
        return $this->getId();
    }

    /**
     *
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return $this
     */
    public function generateSecret()
    {
        $this->secret = uniqid('', true);

        return $this;
    }

    /**
     * @param $passwordGenerationDisabled
     *
     * @return $this
     */
    public function generateSmsCode($passwordGenerationDisabled = false)
    {
        //TODO: replace with real generator
        $this->smsCode = $passwordGenerationDisabled ? 12345 : random_int(10000, 99999);
        $this->smsCodeDt = new \DateTime(null, new \DateTimeZone('UTC'));

        return $this;
    }

    /**
     * @return bool
     */
    public function isSmsCodeExpired()
    {
        if (!$this->smsCodeDt) {
            return false;
        }
        $dt = new \DateTime(null, $this->smsCodeDt->getTimezone());

        return $dt->getTimestamp()  >= $this->smsCodeDt->getTimestamp() + 5 * 60;
    }

    /**
     * @param $password
     *
     * @return bool
     */
    public function checkCredentials($password)
    {
        return $password == hash('sha256', $this->smsCode . $this->secret);
    }

    /**
     * @return $this
     */
    public function clearAuthInfo()
    {
        $this->smsCode = null;
        $this->smsCodeDt = null;

        return $this;
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->phones = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->organizationRoles = new ArrayCollection();
        $this->shopRoles = new ArrayCollection();
        $this->adminRoles = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->chips = new ArrayCollection();
        $this->passports = new ArrayCollection();
        $this->gifts = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->pushes = new ArrayCollection();
        $this->userEvents = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Phone[]
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param Phone[] $phones
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @param Phone $phone
     *
     * @return $this;
     */
    public function addPhone(Phone $phone)
    {
        $phone->setUser($this);
        $this->phones->add($phone);

        return $this;
    }

    /**
     * @return AccessToken[]|ArrayCollection
     */
    public function getAccessTokens()
    {
        return $this->accessTokens;
    }

    /**
     * @param AccessToken[] $accessTokens
     */
    public function setAccessTokens($accessTokens)
    {
        $this->accessTokens = $accessTokens;
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return $this
     */
    public function addAccessToken(AccessToken $accessToken)
    {
        $this->accessTokens->add($accessToken);

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getSmsCode()
    {
        return $this->smsCode;
    }

    /**
     * @param string $smsCode
     */
    public function setSmsCode($smsCode)
    {
        $this->smsCode = $smsCode;
    }

    /**
     * @return \DateTime
     */
    public function getSmsCodeDt()
    {
        return $this->smsCodeDt;
    }

    /**
     * @param \DateTime $smsCodeDt
     */
    public function setSmsCodeDt($smsCodeDt)
    {
        $this->smsCodeDt = $smsCodeDt;
    }

    /**
     * @return SuperAdminAccount
     */
    public function getSuperAdminAccount()
    {
        return $this->superAdminAccount;
    }

    /**
     * @param SuperAdminAccount $superAdminAccount
     *
     * @return $this
     */
    public function setSuperAdminAccount($superAdminAccount)
    {
        $this->superAdminAccount = $superAdminAccount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        $result = false;
        foreach ($this->getRoles() as $role) {
            $result = $result || $role->getRole() == 'ROLE_ADMIN_SUPER_ADMIN';
        }

        return $result;
    }

    /**
     * @return Phone
     *
     * @JMS\VirtualProperty()
     * @JMS\Groups({User::INFO_CARD, User::PUBLIC_CARD})
     * @JMS\SerializedName("phone")
     * @JMS\Inline()
     */
    public function getActivePhone()
    {
        $activePhone = null;
        foreach ($this->phones as $phone) {
            /**
             * @var Phone $phone
             */
            if ($phone->isActual()) {
                $activePhone = $phone;
                break;
            }
        }

        return $activePhone;
    }

    /**
     * @return Passport
     */
    public function getActivePassport()
    {
        foreach ($this->passports as $passport) {
            if ($passport->isActual()) {
                return $passport;
            }
        }

        return null;
    }

    /**
     * @return Chip
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("chip")
     * @JMS\Groups({User::INFO_CARD})
     */
    public function getActiveChip()
    {
        foreach ($this->chips as $chip) {
            /**
             * @var Chip $chip
             */
            if ($chip->isActual()) {
                return $chip;
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection|\PromotionBundle\Entity\UserPromotion[]
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param UserPromotion $promotion
     *
     * @return $this
     */
    public function addPromotion(UserPromotion $promotion)
    {
        if (!$this->promotions->contains($promotion)) {
            $promotion->setUser($this);
            $this->promotions->add($promotion);
        }

        return $this;
    }

    /**
     * @param UserPromotion $promotion
     *
     * @return $this
     */
    public function removePromotion(UserPromotion $promotion)
    {
        if ($this->promotions->contains($promotion)) {
            $promotion->setUser(null);
            $this->promotions->removeElement($promotion);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRequestToken()
    {
        return $this->requestToken;
    }

    /**
     * @param null|string $requestToken
     *
     * @return $this
     */
    public function setRequestToken($requestToken)
    {
        $this->requestToken = $requestToken;

        return $this;
    }

    /**
     * @return ArrayCollection|UserOrganizationRole[]
     */
    public function getOrganizationRoles()
    {
        return $this->organizationRoles;
    }

    /**
     * @param UserOrganizationRole $role
     */
    public function addOrganizationRole(UserOrganizationRole $role)
    {
        $this->organizationRoles->add($role);
    }

    /**
     * @param UserOrganizationRole $role
     */
    public function removeOrganizationRole(UserOrganizationRole $role)
    {
        $this->organizationRoles->remove($role);
    }

    /**
     * @return ArrayCollection
     */
    public function getShopRoles()
    {
        return $this->shopRoles;
    }

    /**
     * @param UserShopRole $role
     */
    public function addShopRole(UserShopRole $role)
    {
        $this->shopRoles->add($role);
    }

    /**
     * @param UserShopRole $role
     */
    public function removeShopRole(UserShopRole $role)
    {
        $this->shopRoles->remove($role);
    }

    /**
     * @return ArrayCollection
     */
    public function getAdminRoles()
    {
        return $this->adminRoles;
    }

    /**
     * @param AdminRole $role
     */
    public function addAdminRole(AdminRole $role)
    {
        $this->adminRoles->add($role);
    }

    /**
     * @param AdminRole $role
     */
    public function removeAdminRole(AdminRole $role)
    {
        $this->adminRoles->remove($role);
    }

    /**
     * @return ArrayCollection
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * @param UserRole $role
     *
     * @return $this
     */
    public function addUserRole(UserRole $role)
    {
        if (!$this->userRoles->contains($role)) {
            $this->userRoles->add($role);
        }

        return $this;
    }

    /**
     * @param UserRole $role
     *
     * @return $this
     */
    public function removeUserRole(UserRole $role)
    {
        if ($this->userRoles->contains($role)) {
            $this->userRoles->removeElement($role);
        }

        return $this;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return OrganizationAdminAccount
     */
    public function getOrganizationAdminAccount()
    {
        return $this->organizationAdminAccount;
    }

    /**
     * @param OrganizationAdminAccount $organizationAdminAccount
     */
    public function setOrganizationAdminAccount($organizationAdminAccount)
    {
        $this->organizationAdminAccount = $organizationAdminAccount;
    }

    /**
     * @return ShopAdminAccount
     */
    public function getShopAdminAccount()
    {
        return $this->shopAdminAccount;
    }

    /**
     * @param ShopAdminAccount $shopAdminAccount
     */
    public function setShopAdminAccount($shopAdminAccount)
    {
        $this->shopAdminAccount = $shopAdminAccount;
    }

    /**
     * @return ArrayCollection
     */
    public function getChips()
    {
        return $this->chips;
    }

    /**
     * @param Chip $chip
     */
    public function addChip(Chip $chip)
    {
        $this->chips->add($chip);
    }

    /**
     * @param Chip $chip
     */
    public function removeChip(Chip $chip)
    {
        $this->chips->remove($chip);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName()
            ?: ($this->getActivePhone()
                ?: $this->getId());
    }

    /**
     * @return ArrayCollection|Passport[]
     */
    public function getPassports()
    {
        return $this->passports;
    }

    /**
     * @param Passport $passport
     *
     * @return $this
     */
    public function addPassport(Passport $passport)
    {
        if (!$this->passports->contains($passport)) {
            $activePassport = $this->getActivePassport();
            if ($activePassport) {
                $activePassport->setUntil(new \DateTime(null, new \DateTimeZone('UTC')));
            }

            $passport->setUser($this);
            $this->passports->add($passport);
            if ($passport->isActual()) {
                $this->firstName = $this->firstName ?: $passport->getFirstName();
                $this->middleName = $this->middleName ?: $passport->getMiddleName();
                $this->lastName = $this->lastName ?: $passport->getLastName();
            }
        }

        return $this;
    }

    /**
     * @param Passport $passport
     *
     * @return $this
     */
    public function removePassport(Passport $passport)
    {
        if ($this->passports->contains($passport)) {
            $passport->setUser(null);
            $this->passports->removeElement($passport);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     *
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("is_confirmed")
     * @JMS\Groups({User::INFO_CARD, User::PUBLIC_CARD})
     *
     * @return bool
     */
    public function isConfirmed()
    {
        $activePassport = array_reduce(
            $this->passports->toArray(),
            function ($result, $passport) {
                /**
                 * @var Passport $passport
                 */
                return $result ?: ($passport->isActual() ? $passport : null);
            },
            null
        );

        return $activePassport !== null;
    }

    /**
     * @return ArrayCollection|\GiftBundle\Entity\Gift[]
     */
    public function getGifts()
    {
        return $this->gifts;
    }

    /**
     * @return bool
     */
    public function isMobileAppInstalled()
    {
        return count($this->accessTokens->toArray()) > 0;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|UserPromotion[]
     */
    public function getActivePromotions()
    {
        return $this->promotions->filter(function (UserPromotion $promotion) {
            return $promotion->isActual();
        });
    }

    /**
     * @param Promotion $promotion
     *
     * @throws ConsistencyException
     *
     * @return UserPromotion|null
     */
    public function getPromotion(Promotion $promotion)
    {
        $equalPromotions = $this->promotions->filter(function (UserPromotion $up) use (&$promotion) {
            return $up->isActual() && $up->getPromotion()->getId() === $promotion->getId();
        });

        if ($equalPromotions->count() > 1) {
            throw new ConsistencyException();
        }

        return $equalPromotions->count() === 0 ? null : $equalPromotions->first();
    }

    /**
     * @param Promotion $promotion
     *
     * @throws ConsistencyException
     *
     * @return bool
     */
    public function hasPromotion(Promotion $promotion)
    {
        return $this->getPromotion($promotion) !== null;
    }

    /**
     * @return ArrayCollection|\SubscriptionBundle\Entity\UserSubscription[]
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param ArrayCollection|\SubscriptionBundle\Entity\UserSubscription[] $subscriptions
     *
     * @return $this
     */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;

        return $this;
    }

    /**
     * @return ArrayCollection|\SubscriptionBundle\Entity\Notification[]
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param ArrayCollection|\SubscriptionBundle\Entity\Notification[] $notifications
     *
     * @return $this
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * @return ArrayCollection|\SubscriptionBundle\Entity\Push[]
     */
    public function getPushes()
    {
        return $this->pushes;
    }

    /**
     * @param ArrayCollection|\SubscriptionBundle\Entity\Push[] $pushes
     *
     * @return $this
     */
    public function setPushes($pushes)
    {
        $this->pushes = $pushes;

        return $this;
    }
}
