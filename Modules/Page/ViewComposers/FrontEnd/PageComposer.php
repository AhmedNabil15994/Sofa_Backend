<?php

namespace Modules\Page\ViewComposers\FrontEnd;

use Modules\Page\Repositories\FrontEnd\PageRepository as Page;
use Illuminate\View\View;
use Cache;

class PageComposer
{
    public $aboutUs;
    public $terms;
    public $privacyPage;
    public $ourBranches;
    public $beforeBuy;

    public function __construct(Page $page)
    {
        $this->aboutUs = $page->getAboutUsPage();
        $this->terms = $page->getTermsPage();
        $this->privacyPage = $page->getPrivacyPage();
        $this->ourBranches = $page->getOurBranchesPage();
        $this->beforeBuy = $page->getBeforeBuyPage();
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(['aboutUs' => $this->aboutUs,'terms' => $this->terms,'privacyPage' => $this->privacyPage,'ourBranches'=>$this->ourBranches,'beforeBuy' =>$this->beforeBuy]);
    }
}
