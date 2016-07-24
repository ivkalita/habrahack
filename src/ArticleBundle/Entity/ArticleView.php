<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ivan Kalita
 * Date: 24.07.16
 * Time: 0:42.
 */
namespace ArticleBundle\Entity;

use AppBundle\Entity\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class ArticleView.
 *
 * @ORM\Entity()
 * @ORM\Table(name="articles__views")
 * @JMS\ExclusionPolicy("all")
 */
class ArticleView extends TimestampableEntity
{
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
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="ArticleBundle\Entity\Article", inversedBy="views")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $article;

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
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     *
     * @return $this
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return \DateTime
     *
     * @JMS\VirtualProperty()
     * @JMS\Groups("all")
     * @JMS\SerializedName("created_at")
     * @JMS\Type("Timestamp")
     */
    public function getCreatedAt()
    {
        return parent::getCreatedAt();
    }
}
