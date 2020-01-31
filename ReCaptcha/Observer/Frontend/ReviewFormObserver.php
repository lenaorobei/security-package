<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\ReCaptcha\Model\CaptchaRequestHandlerInterface;
use Magento\ReCaptcha\Model\Config;

/**
 * ReviewFormObserver
 */
class ReviewFormObserver implements ObserverInterface
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param RedirectInterface $redirect
     * @param Config $config
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        RedirectInterface $redirect,
        Config $config,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->redirect = $redirect;
        $this->config = $config;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_FRONTEND) && $this->config->isEnabledFrontendReview()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->redirect->getRedirectUrl();

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
