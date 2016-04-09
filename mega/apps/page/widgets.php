<?php
namespace Mega\Apps\page;
class widgets extends module{
	
	/*
	 * show list of catalogue
	 * @return array [title,body]
	 */
	public function widgetCatalogues(){
		return $this->moduleWidgetCatalogues();
	}

    /**
     * show last replays on pages
     * @return array [title,body]
     */
    public function lastComments(){
        return $this->moduleLastReplays();
    }
}
