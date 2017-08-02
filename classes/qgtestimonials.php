<?php
class Qgtestimonials extends Module
{
    public function __construct()
    {
        $this->name = 'qgtestimonials';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'jean-roger hess';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('QGTestimonials');
        $this->description = $this->l('This is a testimonial module');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."qgtestimonials`(
            `id_testimonials_article` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(256) NOT NULL,
            `author` VARCHAR(256) NOT NULL,
            `body` TEXT NOT NULL
        )";

        if (!parent::install() ||
            !$this->registerHook('home') ||
            !$this->registerHook('header') ||
            !Db::getInstance()->Execute($sql) ||
            !$this->installTab()
        )
            return false;


        return true;
    }

    public function uninstall()
    {
        $sql = "DROP TABLE `"._DB_PREFIX_."qgtestimonials`";

        if (!parent::uninstall() ||
            !Db::getInstance()->Execute($sql) ||
            !$this->uninstallTab()
        ) {
            return false;
        }

        return true;
    }

    public function hookDisplayHome($params)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('qgtestimonials');
        $articles = Db::getInstance()->executeS($sql);

        $this->context->smarty->assign(
            array(
              'qgtestimonials' => $testimonials
            )
        );

        return $this->display(__FILE__, 'testimonials.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'css/testimonials.css', 'all');
    }


    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'Admintestimonials';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'QGTestimonials';
        }
        $tab->id_parent = 0;

        $tab->module = $this->name;
        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('Admintestimonials');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        } else {
            return false;
        }
    }
}
