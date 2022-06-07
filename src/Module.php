<?php	namespace Morningtrain\WP\PLuginUpdater;

	use Morningtrain\WP\Core\Abstracts\AbstractModule;
    use Morningtrain\WP\PLuginUpdater\Classes\PluginUpdater;

    /**
	 * @package Morningtrain\WP\Rest
	 */
	class Module extends AbstractModule
	{

        protected strign $remote_url;

        public function __construct(strign $remote_url)
        {
            $this->remote_url = $remote_url;
        }

        public function init() {
            $context = $this->getProjectContext();

            new PluginUpdater($this->remote_url, $context->getTextDomain(), $context->getBaseName(), $context->getVersion());
		}
	}