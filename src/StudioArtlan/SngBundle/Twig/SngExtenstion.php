namespace StudioArtlan\SngBundle\Twig;

class SngExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('apirul', array($this, 'apiUrl')),
        );
    }

    public function apiUrl()
    {
		        
    }

    public function getName()
    {
        return 'sng_extension';
    }
}