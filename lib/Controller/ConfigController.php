<?php

namespace OCA\Cookbook\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\Cookbook\Service\RecipeService;
use OCP\IURLGenerator;
use OCA\Cookbook\Service\DbCacheService;

class ConfigController extends Controller {
	/**
	 * @var RecipeService
	 */
	private $service;
	/**
	 * @var IURLGenerator
	 */
	private $urlGenerator;
	
	/**
	 * @var DbCacheService
	 */
	private $dbCacheService;

	public function __construct($AppName, IRequest $request, IURLGenerator $urlGenerator, RecipeService $recipeService, DbCacheService $dbCacheService) {
		parent::__construct($AppName, $request);

		$this->service = $recipeService;
		$this->urlGenerator = $urlGenerator;
		$this->dbCacheService = $dbCacheService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function list() {
		$this->dbCacheService->triggerCheck();
		
		return new DataResponse([
			'folder' => $this->service->getUserFolderPath(),
			'update_interval' => $this->dbCacheService->getSearchIndexUpdateInterval(),
			'print_image' => $this->service->getPrintImage(),
		], Http::STATUS_OK);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function config() {
		$this->dbCacheService->triggerCheck();
		
		if (isset($_POST['folder'])) {
			$this->service->setUserFolderPath($_POST['folder']);
			$this->dbCacheService->updateCache();
		}

		if (isset($_POST['update_interval'])) {
			$this->service->setSearchIndexUpdateInterval($_POST['update_interval']);
		}

		if (isset($_POST['print_image'])) {
			$this->service->setPrintImage((bool)$_POST['print_image']);
		}

		return new DataResponse('OK', Http::STATUS_OK);
	}
	
	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function reindex() {
		$this->dbCacheService->updateCache();

		return new DataResponse('Search index rebuilt successfully', Http::STATUS_OK);
	}
}
