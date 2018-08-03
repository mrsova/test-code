<?php

namespace Admin\Controller;


use Admin\Form\CasesForm;
use Admin\Model\Cases\CasesTable;
use CommonCatalog\View\Helper\CityHelper;
use Zend\Form\FormInterface;
use Zend\Stdlib\Parameters;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class CasesController extends BaseAdminController
{
    /**
     * @var CasesTable
     */
    private $cases;

    /**
     * @var CityHelper
     */
    private $city;

    /**
     * CasesController constructor.
     * @param CasesTable $casesTable
     * @param CityHelper $cityHelper
     */
    public function __construct(CasesTable $casesTable, CityHelper $cityHelper)
    {
        $this->cases = $casesTable;
        $this->city = $cityHelper;
    }

    /**
     * @return array|ViewModel
     * @throws \CommonCatalog\Exceptions\KeyHasUseException
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $formCases = new CasesForm();

        $cityId = $this->city->getCurrentId();
        $page = (int)$this->params()->fromRoute('page', 1);
        $cases = $this->cases->fetchAllForDomainPaginated($cityId);
        $cases->setCurrentPageNumber($page);
        $cases->setItemCountPerPage($this->getNumberObjectsOnPage());
        $this->getMenu($view);
        $view->setVariables(['cases'=> $cases, 'formCases'=> $formCases]);
        $view->setTemplate('admin/cases/index');

        return $view;
    }

    /**
     * @return JsonModel
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        $form = new CasesForm();

        if (!$request->isPost()) {
            return new JsonModel(['error' => true]);
        }

        $form->setInputFilter($form->getInputFilter());
        $data = $request->getPost();

        if ($request->getFiles() !== []) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
        }

        $form->setData($data);

        if (!$form->isValid()) {
            return new JsonModel(['error' => $form->getMessages()]);
        }

        $data = new Parameters($form->getData(FormInterface::VALUES_AS_ARRAY));

        $this->cases->save($data);

        return new JsonModel(['success' => true]);
    }

    /**
     * @return bool|JsonModel
     */
    public function deleteAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return new JsonModel(['error' => true]);
        }

        $id = (int)$request->getPost('id');

        if (!$this->cases->delete($id)) {
            return false;
        }

        return new JsonModel(['success' => true]);

    }
}