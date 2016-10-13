<?php
use Helpers\Assets;
use Helpers\Hooks;
use Helpers\Url;

?>

</div>

<!-- JS -->
<?php
Assets::js([
    Url::templatePath() . 'js/vendor/jquery.js',
    Url::templatePath() . 'js/vendor/what-input.js',
    Url::templatePath() . 'js/vendor/foundation.js',
    Url::templatePath() . 'js/app.js',
]);

?>

</body>
</html>
