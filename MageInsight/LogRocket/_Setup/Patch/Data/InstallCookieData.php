<?php

declare(strict_types=1);

namespace MageInsight\LogRocket\Setup\Patch\Data;

use Amasty\GdprCookie\Model\CookieFactory;
use Amasty\GdprCookie\Model\OptionSource\Cookie\Types as CookieTypes;
use Amasty\GdprCookie\Model\Repository\CookieRepository;
use Amasty\GdprCookie\Model\ResourceModel\Cookie as CookieResource;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallCookieData implements DataPatchInterface
{
    /**
     * @var array[]
     */
    private $cookies = [
        '_lr' => [
            'Description' => "To optimize the website experience for our users",
            'Provider' => 'LogRocket',
            'Type' => CookieTypes::TYPE_3ST_PARTY
        ]
    ];

    private $cookieGroupId = 7;

    /**
     * @var CookieFactory
     */
    private $cookieFactory;

    /**
     * @var CookieRepository
     */
    private $cookieRepository;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    public function __construct(
        CookieFactory $cookieFactory,
        CookieRepository $cookieRepository,
        ModuleDataSetupInterface $moduleDataSetup,
        ResourceInterface $moduleResource
    ) {
        $this->cookieFactory = $cookieFactory;
        $this->cookieRepository = $cookieRepository;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->moduleResource = $moduleResource;
    }

    public function apply()
    {
        $setupDataVersion = (string)$this->moduleResource->getDataVersion('Amasty_GdprCookie');
        foreach ($this->cookies as $name => $cookieData) {
            if (!$setupDataVersion) {
                $cookie = $this->cookieFactory->create();
                $cookie->setName($name);
                $cookie->setDescription($cookieData['Description']);
            } else {
                try {
                    $cookie = $this->cookieRepository->getByName($name);
                } catch (NoSuchEntityException $e) {
                    continue;
                }
            }

            $cookie->setProvider($cookieData['Provider']);
            $cookie->setType($cookieData['Type']);
            $this->cookieRepository->save($cookie);

            if (!$setupDataVersion) {
                $this->moduleDataSetup->getConnection()->update(
                    $this->moduleDataSetup->getTable(CookieResource::TABLE_NAME),
                    ['group_id' => $this->cookieGroupId],
                    ['id = ?' => $cookie->getId()]
                );
            }
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
