<?php
/**
 * Template Name: Theme Features
 * Template Post Type: page
 */

get_header();
?>

<section class="bg-white dark:bg-gray-900">
    <div class="py-16 px-4 mx-auto max-w-screen-xl text-center lg:py-24 lg:px-12">
        <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
            TailsWP. Customizable. Elegant. Functional. Supports Tailwind CSS.
        </h1>
        <p class="mb-8 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 xl:px-48 dark:text-gray-400">
        Discover the perfect blend of elegance and functionality with our TailsWP Theme. Designed to elevate developers and designers alike, our theme is the perfect solution for your next project.
        </p>
        <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
            <a href="#" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                Get Started
                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11.414V14a1 1 0 11-2 0V6.586l-2.293 2.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 6.586z" clip-rule="evenodd"></path>
                </svg>
            </a>
            <a href="#" class="inline-flex justify-center items-center py-3 px-5 text-base font-medium text-center text-gray-900 rounded-lg border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                Learn More
            </a>
        </div>
    </div>
</section>

<section class="dark:bg-gray-900">
    <div class="py-16 px-4 mx-auto max-w-screen-xl lg:py-24 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="feature-card p-8 bg-white dark:bg-gray-900 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Code Playground</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Experiment with your code in real-time using our integrated Code Playground. Supports Tailwind CSS for rapid prototyping and styling.
                </p>
                <a href="" class="inline-flex items-center py-2 px-4 text-white bg-primary-700 hover:bg-primary-800 rounded-lg focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                    Code Playground Demo
                    <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11.414V14a1 1 0 11-2 0V6.586l-2.293 2.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 6.586z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
            <div class="feature-card p-8 bg-white dark:bg-gray-900 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Palette Generator</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Easily customize your theme's colors with our Palette Generator. Change the look and feel of your website with just a few clicks.
                </p>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('palettes-playground'))); ?>" class="inline-flex items-center py-2 px-4 text-white bg-primary-700 hover:bg-primary-800 rounded-lg focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                    Customize Palette
                    <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11.414V14a1 1 0 11-2 0V6.586l-2.293 2.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 6.586z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>