# Auto Excerpt Generator

**Plugin Name:** Auto Excerpt Generator
**Description:** Automatically adds excerpts to posts and pages without excerpts using OpenAI API.
**Version:** 1.11
**Author:** Your Name

## Description

The **Auto Excerpt Generator** plugin is designed to enhance your WordPress site by automatically generating excerpts for posts and pages that do not have them. Utilizing the OpenAI API, this plugin analyzes the content of your posts and creates concise excerpts, improving the readability and SEO of your site.

## Features

- Automatically generates excerpts for posts and pages without excerpts.
- Utilizes the OpenAI API for generating high-quality text.
- Simple settings interface to enter your OpenAI API key.
- Processes old posts on every admin page load to ensure all content is covered.

## Installation

1. **Download the Plugin:**

   - Download the plugin files from the repository or copy the code into a new PHP file named `auto-excerpt-generator.php`.

2. **Upload to WordPress:**

   - Upload the `auto-excerpt-generator` folder to your WordPress installation's `wp-content/plugins/` directory.

3. **Activate the Plugin:**

   - Go to the WordPress admin dashboard, navigate to **Plugins**, and activate the "Auto Excerpt Generator".

4. **Configure Settings:**
   - After activation, go to **Settings > Auto Excerpt Generator** and enter your OpenAI API key.

## Usage

Once activated, the plugin will automatically generate excerpts for any posts or pages that lack them whenever an admin page is loaded. The generated excerpts will be based on the content of each post, ensuring they are relevant and concise.

### Important Notes:

- Ensure you have a valid OpenAI API key, as this is required for the plugin to function properly.
- The plugin only processes published posts and pages.
- Excerpts are generated only once per post; subsequent loads will not trigger re-generation unless the excerpt is manually removed.

## Troubleshooting

- If you encounter issues with the API request, check your OpenAI API key for validity.
- Review error logs in your server or WordPress debug log for any errors related to API requests.
- Ensure that your server can make outgoing HTTP requests.

## Changelog

### 1.11

- Initial release with core functionality to generate excerpts using OpenAI API.

## License

This plugin is licensed under the GPL v2 or later license. You can redistribute it and/or modify it under the terms of this license.

## Support

For support or feature requests, please open an issue in the repository where you downloaded this plugin or contact me directly at [your-email@example.com].
