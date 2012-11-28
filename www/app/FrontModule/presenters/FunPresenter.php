<?php

namespace FrontModule;

/**
 * Created by JetBrains PhpStorm.
 * User: frosty22
 * Date: 28.11.12
 * Time: 22:18
 */
class FunPresenter extends BasePresenter
{

	const PER_PAGE = 30;

	/***/
	public function renderDefault()
	{
		$paginator = $this["paginator"]->getPaginator();
		$paginator->itemsPerPage = self::PER_PAGE;
		$paginator->itemCount = $this->db->table("joke")->count("*");

		$this->template->jokes = $this->db->table("joke")->order("added DESC")
									->limit($paginator->itemsPerPage, $paginator->offset);
	}




}
