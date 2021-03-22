<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{

    public function renderDefault()
    {
        $this->template->username = $this->user->getIdentity();
    }

}
