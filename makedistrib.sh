rm -rf ../distrib
mkdir ../distrib
cd ../distrib
git clone ../permatronc

rm -rf ./.git


export SYMFONY_ENV=prod

cd permatronc
composer install --no-dev --optimize-autoloader
php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump --env=prod --no-debug
php app/console assets:install



zip -rq ../distrib.zip ./
cp installdistrib.php ../

