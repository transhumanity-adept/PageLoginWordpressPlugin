<?php
/*
Template Name: lmslogin_template
*/
?>
<?php
$hasLoginErrors = false;
if (isset($_POST['login'])) {
    $values = $_POST['login'];
    $credentials = array( 'user_login' =>  $values['login'], 'user_password' => $values['password'], 'remember' => isset($values['remember']) );
    $secure_cookie = is_ssl();
    $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, $credentials);
    add_filter('authenticate', 'wp_authenticate_cookie', 30, 3);
    $user = wp_authenticate($credentials['user_login'], $credentials['user_password']);
    if (is_wp_error($user)) {
        $hasLoginErrors = true;
    } else {
        var_dump('true');
        wp_set_auth_cookie($user->ID, $credentials["remember"], $secure_cookie);
        do_action('wp_login', $user->user_login, $user);
        wp_set_current_user($user->ID);
        header('Location: '.get_site_url());
        exit();
    }
}
$registerErrors = false;
if (isset($_POST['register'])) {
    $values = $_POST['register'];
    $userdata = [
        'user_login'           => $values['login'],
        'user_pass'            => $values['password'],
        'user_nicename'        => $values['login'],
        'user_url'             => 'http://wordpress/profile',
        'user_email'           => $values['email'],
        'display_name'         => $values['login'],
        'nickname'             => $values['login'],
        'show_admin_bar_front' => 'false',
        'role'                 => 'user',
        'first_name'           => 'FirstName',
        'last_name'            => 'LastName'
    ];
    $result = wp_insert_user($userdata);
    if(is_wp_error($result)){
        $registerErrors = true;
    }
    else {
        $credentials = array( 'user_login' =>  $values['login'], 'user_password' => $values['password'], 'remember' => isset($values['remember']) );
        $user = wp_authenticate($credentials['user_login'], $credentials['user_password']);
        wp_set_auth_cookie($user->ID, $credentials["remember"], $secure_cookie);
        do_action('wp_login', $user->user_login, $user);
        wp_set_current_user($user->ID);
        header('Location: '.get_site_url());
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <script src="<?php echo plugins_url('node_modules/vue/dist/vue.js', __FILE__) ?>"></script>
    <script src="<?php echo plugins_url('node_modules/vuetify/dist/vuetify.js', __FILE__) ?>"></script>
    <link rel="stylesheet" href="<?php echo plugins_url('node_modules/vuetify/dist/vuetify.css', __FILE__)?>">
    <link rel="stylesheet" href="<?php echo plugins_url('node_modules/@mdi/font/css/materialdesignicons.min.css', __FILE__); ?>">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div id="app">
        <v-app>
            <v-toolbar max-height="70px">
                <v-app-bar-nav-icon></v-app-bar-nav-icon>
                <v-btn text :href="siteUrl">CourseOcean</v-btn>
            </v-toolbar>
            <v-main>
                <v-row class="fill-height" justify="center" align="center">
                    <v-card width="50%" class="pa-6">
                        <v-tabs
                            v-model="currentTab"
                        >
                            <v-tabs-slider color="primary"></v-tabs-slider>
                            <v-tab>Авторизация</v-tab>
                            <v-tab>Регистрация</v-tab>
                            <v-tabs-items
                                v-model="currentTab"
                            >
                                <v-tab-item>
                                    <form class="pb-4 pl-3 pr-3 mt-6" name="login" method="POST">
                                        <v-alert v-if="'<?php echo $hasLoginErrors ? 'true' : 'false'; ?>' === 'true'"
                                                 dense outlined type="error"
                                                 class="mt-3"
                                        >
                                            Ошибка аутентификации
                                        </v-alert>
                                        <v-text-field <?php if($hasLoginErrors) echo 'error' ?> label="Логин" name="login[login]"></v-text-field>
                                        <v-text-field <?php if($hasLoginErrors) echo 'error' ?> label="Пароль" name="login[password]"
                                                      :type="loginShowPassword ? 'password' : 'text'"
                                                      :append-icon="loginShowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                      @click:append="loginShowPassword = !loginShowPassword"></v-text-field>
                                        <v-checkbox <?php if($hasLoginErrors) echo 'error' ?> name="login[remember]" label="Запомнить меня"></v-checkbox>
                                        <v-btn width="100%" type="submit">Войти</v-btn>
                                    </form>
                                </v-tab-item>
                                <v-tab-item>
                                    <form class="pb-4" name="register" method="POST">
                                        <v-alert v-if="'<?php echo $registerErrors ? 'true' : 'false'; ?>' === 'true'"
                                                 dense outlined type="error"
                                                 class="mt-3"
                                        >
                                            Ошибка регистрации
                                        </v-alert>
                                        <v-text-field <?php if($registerErrors) echo 'error' ?> label="Логин" name="register[login]"></v-text-field>
                                        <v-text-field <?php if($registerErrors) echo 'error' ?> label="Почта" name="register[email]"></v-text-field>
                                        <v-text-field <?php if($registerErrors) echo 'error' ?> label="Пароль" name="register[password]"
                                                      :type="registerShowPassword ? 'password' : 'text'"
                                                      :append-icon="registerShowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                                      @click:append="registerShowPassword = !registerShowPassword"></v-text-field>
                                        <v-btn width="100%" type="submit">Зарегистрироваться</v-btn>
                                    </form>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-tabs>
                    </v-card>
                </v-row>
            </v-main>
        </v-app>
    </div>
</body>
</html>
<script>
    var appPlugin = new Vue({
        el: '#app',
        vuetify: new Vuetify(),
        data: {
            siteUrl: '<?php echo get_site_url(); ?>',
            currentTab: <?php echo empty($registerErrors) ? 0 : 1 ?>,
            loginShowPassword: true,
            registerShowPassword: true,
        },
    });
</script>
<style>
    body, html {
        overflow-y: auto;
    }
    .row {
        margin: 0px !important;
    }
</style>
