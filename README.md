
This is a symfony application

# Initialisation:

Install vendor packages with : composer install
http://symfony.com/fr/doc/current/book/installation.html#option-1-composer

# Configuration

Change app/config_prod.yml:

site:
    directory: ../../PERMATRONC_data/

# Change html:

Twig files are in the directory : /src/SiteBundle/Resources/views/Default

# Apache:

apache root directory should be /web : http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html


