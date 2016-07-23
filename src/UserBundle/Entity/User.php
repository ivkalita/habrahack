<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 11.02.16
 * Time: 12:26.
 */
namespace UserBundle\Entity;

use AppBundle\Entity\City;
use AppBundle\Entity\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
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
     * @var string|null
     */
    protected $requestToken;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", inversedBy="users")
     *
     * @JMS\Expose()
     * @JMS\Groups({User::INFO_CARD})
     */
    protected $city;

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
     * @return string[]
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return string
     */
    public function getPassword()
    {
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
     * @return string
     */
    public function __toString()
    {
        return $this->getFullName()
            ?: ($this->getActivePhone()
                ?: $this->getId());
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
     * @return bool
     */
    public function isMobileAppInstalled()
    {
        return count($this->accessTokens->toArray()) > 0;
    }
}
