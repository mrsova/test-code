<?php

namespace Admin\Model\Cases;

use CommonCatalog\BaseClass\AbstractBaseModelStatic;
use CommonCatalog\Models\Video\VideoYouTube;

class Cases extends AbstractBaseModelStatic
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $area;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $typeClass;

    /**
     * @var string
     */
    protected $demands;

    /**
     * @var string
     */
    protected $results;

    /**
     * @var string
     */
    protected $video;

    /**
     * @var string
     */
    protected $logoObject;

    /**
     * @var int
     */
    protected $cityId;

    /**
     * @var string
     */
    protected $videoId;

    /**
     * @var string
     */
    protected $videoImage;

    /**
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : 0;
        $this->title = !empty($data['title']) ? $data['title'] : '';
        $this->area = !empty($data['area']) ? $data['area'] : '';
        $this->address = !empty($data['address']) ? $data['address'] : '';
        $this->typeClass = !empty($data['type_class']) ? $data['type_class'] : '';
        $this->demands = !empty($data['demands']) ? $data['demands'] : '';
        $this->results = !empty($data['results']) ? $data['results'] : '';
        $this->video = !empty($data['video']) ? $data['video'] : '';
        $this->logoObject = !empty($data['logo_object']) ? $data['logo_object'] : '';
        $this->cityId = !empty($data['city_id']) ? $data['city_id'] : 0;

        $youtube = new VideoYouTube($this->video);

        $this->videoId = $youtube->getVideoId();
        $this->videoImage = $youtube->getThumbnailURL()();


    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getTypeClass(): string
    {
        return $this->typeClass;
    }

    /**
     * @return string
     */
    public function getDemands(): string
    {
        return $this->demands;
    }

    /**
     * @return string
     */
    public function getResults(): string
    {
        return $this->results;
    }

    /**
     * @return string
     */
    public function getLogoObject(): string
    {
        return $this->logoObject;
    }

    /**
     * @return int
     */
    public function getCityId(): int
    {
        return $this->cityId;
    }

    /**
     * @return string
     */
    public function getArea(): string
    {
        return $this->area;
    }


    /**
     * @return bool
     */
    public function isVideo(): bool
    {
        if(!$this->video){
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getVideoId(): string
    {
        return $this->videoId;
    }

    /**
     * @return string
     */
    public function getVideoImage(): string
    {
        return $this->videoImage;
    }
}