<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 23.07.16
 * Time: 19:48
 */

namespace ArticleBundle\Entity;


use AppBundle\Entity\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Article
 * @package ArticleBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="articles__articles")
 * @JMS\ExclusionPolicy("all")
 */
class Article extends TimestampableEntity
{
    const FULL_CARD = 'article__full';
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose()
     * @JMS\Groups("all")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=1000)
     *
     * @JMS\Expose()
     * @JMS\Groups("all")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="video_url", type="string", length=3000)
     *
     * @JMS\Expose()
     * @JMS\Groups("all")
     */
    protected $videoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="placeholder_url", type="string", length=3000)
     *
     * @JMS\Expose()
     * @JMS\Groups("all")
     */
    protected $placeholderUrl;

    /**
     * @var ArticleView[]
     *
     * @ORM\OneToMany(targetEntity="ArticleBundle\Entity\ArticleView", mappedBy="article", orphanRemoval=true)
     */
    protected $views;

    public function __construct()
    {
        $this->views = new ArrayCollection();
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
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * @param string $videoUrl
     *
     * @return $this
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholderUrl()
    {
        return $this->placeholderUrl;
    }

    /**
     * @param string $placeholderUrl
     *
     * @return $this
     */
    public function setPlaceholderUrl($placeholderUrl)
    {
        $this->placeholderUrl = $placeholderUrl;

        return $this;
    }

    /**
     * @return \DateTime
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("created_at")
     * @JMS\Type("Timestamp")
     * @JMS\Groups("all")
     */
    public function getCreatedAt()
    {
        return parent::getCreatedAt();
    }

    /**
     * @return ArticleView[]
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param ArticleView[] $views
     *
     * @return $this
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }
}