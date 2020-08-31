<?php
namespace Base\Dispatch;

use Base\Context;

class DispatchException extends \Exception
{
    function process()
    {
        echo 'Произошло исключение в диспетчере: ' . $this->getMessage() . '<br><br>';
        $context = Context::getInstance();
        echo 'Запрошенный модуль: ' . $context->getRequest()->getRequestModule() . '<br>';
        echo 'Запрошенный контроллер: ' . $context->getRequest()->getRequestController() . '<br>';
        echo 'Запрошенный экшен: ' . $context->getRequest()->getRequestAction() . '<br><br><br>';
        echo '<br>';
        echo 'Вызванный модуль: ' . $context->getDispatcher()->getModuleName() . '<br>';
        echo 'Вызванный контроллер: ' . $context->getDispatcher()->getControllerName() . '<br>';
        echo 'Вызванный экшен: ' . $context->getDispatcher()->getActionName() . '<br><br><br>';
    }
}
