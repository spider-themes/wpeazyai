<?php
// Function to send a request to OpenAI's API
function openai_request($prompt) {
    $apiKey = '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'; // Replace 'your-api-key' with your actual API key
    $data = array(
        'model' => 'ft:gpt-3.5-turbo-0125:md-jewel::93jSQ3fE',
		'temperature'=>1,
		  'max_tokens'=>256,
		  'top_p'=>1,
		  'frequency_penalty'=>0,
		  'presence_penalty'=>0,
        'messages' => array(
				array("role" => "system", 
                "content" => "Keep exact assistance,do not modify my assistance,show me same assistance"),
				array(
                "role" => "user", 
                "content" => "Demo Import"
                   ),
				array(
                "role" => "assistant", 
                "content" => "Oil,Salt,Soap,Powder,Rice,Sugar etc"
                 ),

                 array(
                    "role" => "user", 
                    "content" => "Rogan Documentation"
                       ),
                    array(
                    "role" => "assistant", 
                    "content" => "Spot of come to ever hand as lady meet on. Delicate contempt received two yet advanced. Gentleman as belonging he commanded believing dejection in by.hello button Secondary Button Third button Getting Started Before building your site, better take a look at this section.FAQs Supposed to be commonly asked issues, questions & troubleshoot tricks are in Tips / Guide Quos vesti bulum dictum irure recusandae, aliquet, ullam quod fugit, dictumst, optio,Theme Settings You can control the theme’s functionality and customize theme’s content from the Shortcodes This section will introduce you how to use the Faster‘s shortcodes. Those Visual Composer Visual Composer is a unique plugin, it will help you manage your Changes Log—– 1.1 (26 april 2018) —— Added: One click demo importer New:"
                     ),
                     array(
                        "role" => "user", 
                        "content" => "Theme Installation"
                           ),
                        array(
                        "role" => "assistant", 
                        "content" => "Extract the zipped package downloaded from ThemeForest to your desktop, in the extracted package you will find the aprimo.zip file which is the WordPress theme.You can install the theme in two ways:FTP: Extract aprimo.zip file and upload the extracted folder to /wp-content/themes/ folder on your server.WordPress: Navigate to Appearance -> Add New Themes -> Upload page. Select aprimo.zip file. Press the Install Now button to upload and install the theme.After uploading the theme, you have to activate it. Navigate to Appearance -> Themes page to activate the theme."
                         ),
                         array(
                            "role" => "user", 
                            "content" => "Last depth"
                               ),
                            array(
                            "role" => "assistant", 
                            "content" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. jj"
                             ),

                             array(
                                "role" => "user", 
                                "content" => "Required plugins"
                                   ),
                                array(
                                "role" => "assistant", 
                                "content" => "After installing the theme you most have to install the ‘Faster Core’, ‘Visual composer’ and ‘Codestar Framework’ plugins to import the demo content as well and work the theme correctly.You will find all required and recommended plugins at there Appearance > Install Plugins."
                                 ),
                                 array(
                                    "role" => "user", 
                                    "content" => "FAQs"
                                       ),
                                    array(
                                    "role" => "assistant", 
                                    "content" => "Supposed to be commonly asked issues, questions & troubleshoot tricks are in this section.Articles Updating Your Theme How to set site’s favicon How to Paginate a Post ? How to change Author Avatar?"
                                     ),

                                     array(
                                        "role" => "user", 
                                        "content" => "Updating Your Theme"
                                           ),
                                        array(
                                        "role" => "assistant", 
                                        "content" => "We are strongly recommended to install “Envato Toolkit” plugin for ease of use. Once necessary plugin installed and activated “Envato Toolkit” menu will be available at your admin side.Important : If you have missing plugins or having issues to install any of them, all the related could be found under “recommended-plugins” folder within the downloaded package.User Account Information To obtain your API Key, visit your “My Settings” page on any of the Envato Marketplaces. Once a valid connection has been made any changes to the API key below for this username will not effect the results for 5 minutes because they’re cached in the database. If you have already made an API connection and just purchase a theme and it’s not showing up, wait five minutes and refresh the page. If the theme is still not showing up, it’s possible the author has not made it available for auto-install yet.Marketplace Username: Enter your Envato marketplace username.Secret API Key: Enter your Envato marketplace Secret API Key (http://themeforest.net/user/[your-username]/api_keys/edit, Login to your Themeforest account, Settings > API Key) Backup Information This plugin will automatically save your theme as a ZIP archive before it does an upgrade. The directory those backups get saved to is wp-contentenvato-backups. However, if you’re experiencing problems while attempting to upgrade, it’s likely to be a permissions issue and you may want to manually backup your theme before upgrading. Alternatively, if you don’t want to backup your theme you can check the box below.Skip Theme Backup: Strongly recommended keep unchecked.Alternative Way You can also update the theme by replacing the old theme folder. First go to your theme directory then deletethe theme folder and paste the new version of the theme."
                                         ),
                                         array(
                                            "role" => "user", 
                                            "content" => "How to set site’s favicon"
                                               ),
                                            array(
                                            "role" => "assistant", 
                                            "content" => "Go to Appearance > Customize > Site Identity. Then Select an image as site icon. It’s recommended to use a .ico image as the site icon (favicon)."
                                             ),
                                             array(
                                                "role" => "user", 
                                                "content" => "How to Paginate a Post ?"
                                                   ),
                                                array(
                                                "role" => "assistant", 
                                                "content" => "In order to paginate your blog post, in the Write panel in your WordPress admin, switch to the TEXT view (if you are using the Visual view) and then enter the following code to wherever you want to break the post up into a new page:<!--nextpage-->"
                                                 ),
                                                 array(
                                                    "role" => "user", 
                                                    "content" => "How to change Author Avatar?"
                                                       ),
                                                    array(
                                                    "role" => "assistant", 
                                                    "content" => "The theme uses Gravatar to display users avatars . to create or change your Gravatar :If you have never setup a Gravatar account:Register/login to Gravatar. Upon registering, use the SAME email address that is associated with your WordPress User Profile.Upload your photo and associate it with that specific email address.If you already registered at gravatar.com, follow these steps:Click on “My Gravatars”.click on “Add a New Email”.Then, you’ll need to click on “Add a New Image”.Upload an image.Then select the email address, and then select your image from the bottom. Now it’s associated with that email address. Click Save.It will take about 5-10 minutes to propagate on the internet, and then will appear on site."
                                                     ),

                                                     array(
                                                        "role" => "user", 
                                                        "content" => "Tips / Guide"
                                                           ),
                                                        array(
                                                        "role" => "assistant", 
                                                        "content" => "Quos vesti bulum dictum irure recusandae, aliquet, ullam quod fugit, dictumst, optio, parturient auctor pulvinar, inceptos sem. Habitasse! Mattis, litora ipsum, fringilla elementum.
                                                        Articles
                                                        Under Tips Contribute
                                                        Optimize Images
                                                        Language Translations
                                                        How to change Author Avatar?
                                                        How to change Author Avatar?"
                                                         ),
                                         array(
                                          "role" => "user", 
                                         "content" => "Under Tips Contribute"
                                             ),
                                         array(
                                            "role" => "assistant", 
                                            "content" => "01618505996"
                                             ),
                                     array(
                                     "role" => "user", 
                                    "content" => "Optimize Images"
                                      ),
                                     array(
                                    "role" => "assistant", 
                                         "content" => "Optimizing your images will helps your WordPress site load faster.
                                         Download and activate WP Smush.it This plugin will optimize every image you upload and you can use the Bulk Smush.it feature to smush all of your uploaded images.
                                         Alternatives to WP Smush.it include EWWW Image Optimizer, CW Image Optimizer, Imsanity and Hammy. Many WordPress users prefer using these plugins as they do not rely on an external server to process your images"
                                         ),

                                         array(
                                            "role" => "user", 
                                            "content" => "Language Translations"
                                               ),
                                            array(
                                            "role" => "assistant", 
                                            "content" => "All of our Themes are able to be translated into any language. The process of translating the theme is a user responsibility. The following instructions are given as guidance.
                                            Open wp-config.php and replace this: define (WPLANG,); with this (substitute the language string (bg_BG) with your own!): define (WPLANG, bg_BG);
                                            Download and install POEDIT
                                            Connect to your site -> open your theme/languages directory
                                            Download the default.pot file and open it with POEDIT.
                                            Translate file and save it as bg_BG (the file name must match with the string you inserted into wp-config.php)
                                            Two files will be generated after save. bg_BG.pot and bg_BG.mo
                                            Upload the .mo and .pot files into wp-content/themes/your-theme/languages folder
                                            Your WordPress installation should be in the language you intend on using the site."
                                             ),

                                         array(
                                         "role" => "user", 
                                        "content" => "How to change Author Avatar?"
                                                   ),
                                         array(
                                        "role" => "assistant", 
                                        "content" => "The theme uses Gravatar to display users avatars . to create or change your Gravatar :
                                        If you have never setup a Gravatar account:
                                        Register/login to Gravatar. Upon registering, use the SAME email address that is associated with your WordPress User Profile.
                                        Upload your photo and associate it with that specific email address.
                                        If you already registered at gravatar.com, follow these steps:
                                        Click on “My Gravatars”.
                                        click on “Add a New Email”.
                                        Then, you’ll need to click on “Add a New Image”.
                                        Upload an image.
                                        Then select the email address, and then select your image from the bottom. Now it’s associated with that email address. Click Save.
                                        It will take about 5-10 minutes to propagate on the internet, and then will appear on site."
                                                 ),
                                             array(
                                                    "role" => "user", 
                                                    "content" => "Theme Settings"
                                                       ),
                                                 array(
                                                    "role" => "assistant", 
                                                    "content" => "ou can control the theme’s functionality and customize theme’s content from the Theme Settings page. Here you will find all relevant settings to customize the theme.
                                                    This section will provide you an in-depth guide on how to use the Theme Options. To access the Theme Settings page, go to- Dashboard > Theme Settings. You will find this page at the top of the Appearance menu.
                                                    Articles
                                                    Header Settings
                                                    Footer Settings
                                                    Reprehenderit bibendum
                                                    Iusto molestie distinctio"
                                                     ),
                                                     array(
                                                        "role" => "user", 
                                                        "content" => "Header Settings"
                                                           ),
                                                        array(
                                                        "role" => "assistant", 
                                                        "content" => "In the Header settings section, you will get a lot of header customization options. In this section, you can change the logo and change the page/post’s Title bar padding, Page margin-top, Title bar overlay color etc."
                                                         ),


				
				array("role" => "user", "content" => $prompt)
					)
			);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $apiKey"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return json_decode($response, true)['choices'][0]['message']['content'];
    }
}

// Example usage
$user_input = $_POST['msg'];
$response = openai_request($user_input);
echo "<br>Bot: " . $response;