# Albums Wow Theme

Albums Wow is a simple and powerful WordPress theme designed specifically for managing and displaying albums with custom post types and taxonomies. The theme comes with metabox support, custom queries, and seamless integration with Elementor.

## Features

- **Custom Post Type**: `Albums`
- **Custom Taxonomies**: `Single` and `Genre`
- **Metaboxes**: Add custom metadata to albums
- **Elementor Widget**: Includes a custom widget for Elementor that allows querying albums via `WP_Query` or `wpdb` (optional)

## Installation

1. Clone the repository to your WordPress `wp-content/themes` directory:

    ```bash
    git clone https://github.com/massenjoy-full/albums-wow.git
    ```

2. Activate the theme via the WordPress Admin Dashboard:
   - Go to `Appearance > Themes`.
   - Activate the **Albums Wow** theme.

3. Ensure that Elementor is installed and activated if you plan to use the Elementor widget.

## Custom Post Type & Taxonomies

- **Albums**: A custom post type to showcase albums.
- **Single**: Custom taxonomy to categorize albums based on singles.
- **Genre**: Custom taxonomy to categorize albums by genre.

## Metaboxes

The theme includes metaboxes for the Albums custom post type, allowing you to attach metadata to each album, such as release dates, labels, or other custom fields.

## Elementor Integration

Albums Wow includes a custom Elementor widget that allows you to query albums using either `WP_Query` or `wpdb` (depending on your preference). You can customize your queries dynamically through the Elementor interface to display albums based on your specified parameters.

## Shortcodes

Albums Wow theme also provides powerful shortcodes to display albums with dynamic queries. Here’s how you can use them:

### [albums_wow_lists]

This shortcode uses `WP_Query` to display albums. You can customize the query by passing attributes.

#### Available Attributes:

- `posts_per_page`: Number of posts to display per page (default: value from `get_option('posts_per_page')`).
- `orderby`: Order the posts by a specific field (default: `date`).
- `order`: Sort the posts in ascending (`asc`) or descending (`desc`) order (default: `desc`).
- `single`: Filter by single taxonomy (default: empty).
- `genre`: Filter by genre taxonomy (default: empty).
- `hide_filters`: Boolean to show or hide filters (default: `false`).

#### Example Usage:

```php
[albums_wow_lists posts_per_page="5" orderby="title" order="asc" genre="rock"]
```
This will display 5 albums, sorted by title in ascending order, filtered by the "rock" genre.
### [albums_wow_lists_wpdb]

This shortcode uses `wpdb` for custom SQL queries to display albums. You can pass a custom query through the attributes.

#### Available Attributes:

- `query`: Custom SQL query to run (default: `empty`).
- `hide_filters`: Boolean to show or hide filters (default: `false`).

#### Example Usage:

```php
[albums_wow_lists_wpdb query="posts_per_page=10&post_status=draft&genre=rock" hide_filters="true"]
```
This will display 10 albums, sorted by title in ascending order, filtered by the "rock" genre. Filters hidden.

# Gulp
If you have made changes to styles and/or scripts:

Installing the required packages:
```bash
npm install
```

Updating css/js files:
```bash
npx gulp
```