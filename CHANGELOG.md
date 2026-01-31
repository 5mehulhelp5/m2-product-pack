# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-12-19
### Changed
- Restructured module from `src/` subdirectory to root directory for better Composer compatibility
- Updated namespace and autoloading structure

### Added
- Added product pack search results interface for API operations
- Added cart item renderer for pack product type
- Added product view block for pack type products
- Added product type transition manager plugin for pack products
- Added dedicated price model for pack products
- Added resource model and collection for pack options

### Improved
- Improved module structure following Magento 2 best practices

## [1.1.0] - 2023-10-31
### Added
- Added unique constraints on database schema for pack option integrity
- Implemented data integrity checks for pack option assignments

### Fixed
- Fixed duplicate pack option entries in database
- Improved database schema with proper unique indexes

## [1.0.7] - 2023-10-31
### Changed
- Version update for Packagist release

## [1.0.6] - 2023-10-31
### Changed
- Database schema improvements

## [1.0.5] - 2023-02-11
### Added
- Added Hyva theme compatibility for product pack frontend
- Implemented Hyva-specific templates for pack option selection
- Added Hyva checkout cart integration for pack products
- Added Hyva sales order item renderers for displaying pack information
- Implemented Alpine.js-based pack option selection for Hyva theme

### Changed
- Refactored frontend templates to support both Luma and Hyva themes

## [1.0.4] - 2023-02-11
### Added
- Added dynamic price calculation display on product detail page when pack options are selected
- Implemented real-time price updates based on selected pack quantity and discount type
- Added admin configuration option to enable/disable calculated price display on PDP
- Added special price calculation support with two methods: "Best Price" and "Proportional"
- Implemented configurable special price calculation type in admin system configuration

### Changed
- Enhanced JavaScript productpack.js to handle real-time price calculations
- Updated price helper to support special price calculations for pack options
- Changed default pack quantity from 0 to 1 for better user experience

### Fixed
- Removed debug code from JavaScript files

## [1.0.3] - 2023-02-11
### Added
- Added pack product information display in admin order view
- Added pack information in admin invoice view
- Added pack information in admin credit memo view
- Added pack information in admin shipment view
- Implemented pack details display in frontend transactional emails (order, invoice, credit memo, shipment)
- Added pack information rendering in customer account order view
- Implemented dedicated templates for displaying pack item details across all order-related pages

### Fixed
- Fixed add-to-cart functionality from product listing pages for pack products
- Improved pack option handling for composite products
- Enhanced cart and sales item info templates for better pack display

## [1.0.2] - 2023-02-11
### Fixed
- Fixed namespace references across all module files
- Corrected Nanobots namespace to Qoliber namespace
- Updated composer.json package name and autoloading configuration

## [1.0.1] - 2023-02-10
### Fixed
- Fixed file structure for Composer package compatibility
- Moved module files from `src/Nanobots/ProductPack/` to `src/` directory

## [1.0.0] - 2023-02-10
### Added
- Initial release of Product Pack module
- Implemented custom "pack" product type for creating bundled quantity offers
- Added pack option management interface in admin product edit form
- Implemented dynamic rows component for managing multiple pack options
- Added pack option repository and data models for option persistence
- Implemented price calculation supporting fixed amount and percentage discounts
- Added pack option display on product detail page with quantity selector
- Implemented frontend JavaScript for pack option selection and validation
- Added cart integration for pack products with custom item rendering
- Implemented checkout integration for pack products
- Added quote integration with pack option data persistence
- Implemented order conversion with pack information transfer from quote to order
- Added inventory deduction handling for pack products
- Implemented credit memo support for pack product returns
- Added shipment integration for pack products
- Implemented MSI (Multi-Source Inventory) reservation handling for pack products
- Added extension attributes for pack options on quote and order items
- Implemented product configuration helper for pack option display
- Added price helper for calculating pack prices with discounts
- Implemented type helper for pack product identification
- Added admin ACL resources for pack option type management
- Implemented system configuration options for pack display preferences
- Added database schema for pack options storage (qoliber_product_pack_option table)
- Implemented unique constraints on product_id and option_sku columns
- Added support for multiple pack options per product
- Implemented pack option label and SKU management
- Added sort order functionality for pack options
- Implemented RequireJS configuration for frontend modules
- Added LESS styling for pack option display in Luma theme
- Implemented comprehensive integration tests for pack functionality
