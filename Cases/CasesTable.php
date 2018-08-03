<?php

namespace Admin\Model\Cases;

use CommonCatalog\BaseClass\AbstractBaseModel;
use Core\Service\Images;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CasesTable extends AbstractBaseModel
{
    /**
     * directory for upload images
     */
    CONST DIRECTORY = 'cases';

    /**
     * @var Images
     */
    private $coreImages;


    /**
     * CasesTable constructor.
     * @param TableGateway $tableGateway
     * @param Images $coreImages
     */
    public function __construct(TableGateway $tableGateway, Images $coreImages)
    {
        parent::__construct($tableGateway);

        $this->coreImages = $coreImages;
    }


    /**
     * @param $cityId
     * @return CasesCollection
     * @throws \CommonCatalog\Exceptions\KeyHasUseException
     */
    public function fetchAllForDomain($cityId)
    {
        $resultSet = $this->tableGateway->select(function (Select $select) use ($cityId) {
            $select->where('city_id = ' . $cityId);
            $select->order('id DESC');
        });

        return $this->getCollectionObjects($resultSet);
    }

    /**
     * @param $cityId
     * @return Paginator
     */
    public function fetchAllForDomainPaginated($cityId)
    {
        $select = new Select($this->tableGateway->getTable());
        $paginatorAdapter = new DbSelect(
            $select->where('city_id = ' . $cityId)->order('id DESC')->limit(3),
            $this->getAdapter(),
            $this->tableGateway->getResultSetPrototype()
        );

        return new Paginator($paginatorAdapter);
    }


    /**
     * @param int $id
     * @return array|Cases|null
     */
    public function getRow(int $id)
    {
        return $this->tableGateway->select(['id' => $id])->current();
    }


    /**
     * @param $in_data
     * @return bool|int
     */
    public function save($in_data)
    {
        $data = [
            'title' => $in_data->title,
            'area' => $in_data->area,
            'address' => $in_data->address,
            'type_class' => $in_data->typeClass,
            'demands' => $in_data->demands,
            'results' => $in_data->results,
            'video' => $in_data->video,
            'city_id' => (int)$in_data->cityId,
        ];

        $id = (int)$in_data->id;

        if ($id == 0) {
            if (!$this->tableGateway->insert($data)) {
                return false;
            }

            $id = $this->lastInsertValue();
            $this->coreImages->saveImages([
                'logo_object' => $in_data->logoObject,
            ], static::DIRECTORY, $id);

            if ($images = $this->coreImages->getDataImages()) {
                return !empty($images) ? $this->tableGateway->update($images, ['id' => $id]) : false;
            }
        }

        if (!$this->getRow($id)) {
            return false;
        }

        $this->tableGateway->update($data, ['id' => $id]);

        if (is_array($in_data->logoObject)) {
            if ($this->getRow($id)->getLogoObject()) {
                $this->coreImages->DeleteFile(SITE_ROOT . $this->getRow($id)->getLogoObject());
            }

            $this->coreImages->saveImages(['logo_object' => $in_data->logoObject], static::DIRECTORY, $id);
        }

        if ($images = $this->coreImages->getDataImages()) {
            return !empty($images) ? $this->tableGateway->update($images, ['id' => $id]) : false;
        }

        return true;

    }

    /**
     * @param int $id
     * @return bool|int
     */
    public function delete(int $id)
    {
        if (!$id) {
            return false;
        }

        if (!$this->getRow($id)) {
            return false;
        }

        if ($this->getRow($id)->getLogoObject() || $this->getRow($id)->getLogoCompany()) {
            $this->coreImages->DeleteDirectory(static::DIRECTORY . '/' . $id);
        }

        return $this->tableGateway->delete(['id' => $id]);
    }


    /**
     * @return int
     */
    public function lastInsertValue()
    {
        return $this->tableGateway->adapter->getDriver()->getConnection()->getLastGeneratedValue("common_cases_id_seq");
    }

    /**
     * @param ResultSet $resultSet
     * @return CasesCollection
     * @throws \CommonCatalog\Exceptions\KeyHasUseException
     */
    protected function getCollectionObjects(ResultSet $resultSet)
    {
        $sidebarCollection = new CasesCollection();

        while ($resultSet->valid()) {
            $result = $resultSet->current();
            $sidebarCollection->addItem($result);
            $resultSet->next();
        }
        return $sidebarCollection;
    }

}