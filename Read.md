# E-Invoicing Platform

A Laravel-based multi-tenant e-invoicing platform for sellers to manage buyers, create invoices, bulk-import invoices through CSV, generate invoice documents, track payments, and automate invoice email processing.

---

# Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [System Architecture](#3-system-architecture)
4. [Database Structure](#4-database-structure)
5. [User and Seller Flow](#5-user-and-seller-flow)
6. [Seller Isolation](#6-seller-isolation)
7. [Buyer Management](#7-buyer-management)
8. [Buyer Matching](#8-buyer-matching)
9. [Duplicate Buyer Prevention](#9-duplicate-buyer-prevention)
10. [Invoice Structure](#10-invoice-structure)
11. [Invoice Items](#11-invoice-items)
12. [Invoice Creation Flow](#12-invoice-creation-flow)
13. [Invoice Service](#13-invoice-service)
14. [Invoice Numbering](#14-invoice-numbering)
15. [Payment Status](#15-payment-status)
16. [Invoice Listing](#16-invoice-listing)
17. [Invoice Filtering](#17-invoice-filtering)
18. [Invoice Pagination](#18-invoice-pagination)
19. [CSV Invoice Import](#19-csv-invoice-import)
20. [CSV Validation](#20-csv-validation)
21. [CSV Buyer Matching](#21-csv-buyer-matching)
22. [Duplicate Invoice Prevention](#22-duplicate-invoice-prevention)
23. [Database Duplicate Protection](#23-database-duplicate-protection)
24. [Import Results](#24-import-results)
25. [Large CSV Imports](#25-large-csv-imports)
26. [CSV Import Jobs](#26-csv-import-jobs)
27. [Subscription Plans](#27-subscription-plans)
28. [Invoice Limits](#28-invoice-limits)
29. [Invoice Limit Database Trigger](#29-invoice-limit-database-trigger)
30. [PDF Invoice](#30-pdf-invoice)
31. [Email Automation](#31-email-automation)
32. [Email Jobs](#32-email-jobs)
33. [Transactions](#33-transactions)
34. [Controllers and Services](#34-controllers-and-services)
35. [Backend Structure](#35-backend-structure)
36. [Frontend Structure](#36-frontend-structure)
37. [Routes](#37-routes)
38. [Middleware and Authentication](#38-middleware-and-authentication)
39. [Seeders](#39-seeders)
40. [10,000 Invoice Seeder](#40-10000-invoice-seeder)
41. [Seeder Performance](#41-seeder-performance)
42. [Running Seeders](#42-running-seeders)
43. [Testing 10,000 Invoices](#43-testing-10000-invoices)
44. [Seller Isolation Testing](#44-seller-isolation-testing)
45. [CSV Import Testing](#45-csv-import-testing)
46. [Duplicate Import Testing](#46-duplicate-import-testing)
47. [Wrong Seller Testing](#47-wrong-seller-testing)
48. [Invoice Business Rules](#48-invoice-business-rules)
49. [CSV Import Business Rules](#49-csv-import-business-rules)
50. [Security Rules](#50-security-rules)
51. [Queues](#51-queues)
52. [Performance Considerations](#52-performance-considerations)
53. [Recommended Development Flow](#53-recommended-development-flow)
54. [Large Dataset Testing](#54-large-dataset-testing)
55. [Production Checklist](#55-production-checklist)
56. [Golden Rules](#56-golden-rules)
57. [Complete Invoice Flow](#57-complete-invoice-flow)
58. [Complete CSV Import Flow](#58-complete-csv-import-flow)
59. [Complete Email Flow](#59-complete-email-flow)
60. [Final Architecture](#60-final-architecture)

---

# 1. System Overview

The E-Invoicing Platform is a multi-tenant Laravel application.

The main purpose of the system is to allow different sellers or companies to manage their own:

- Buyers
- Invoices
- Invoice Items
- Payments
- CSV Imports
- Email Jobs
- Invoice Documents
- Subscription Limits

The most important rule in the system is:

> A seller owns its own business data.

A buyer created by Seller A must not be matched to Seller B.

An invoice created by Seller A must not be visible to Seller B.

A CSV imported by Seller A must only use buyers belonging to Seller A.

---

# 2. Technology Stack

## Backend

- Laravel 13
- PHP 8.3+
- MySQL 8+
- Eloquent ORM
- Laravel Services
- Laravel Jobs
- Laravel Queues

## Frontend

- Vue 3
- TypeScript
- Inertia.js
- Tailwind CSS
- Lucide Icons

## Development Tools

- Composer
- NPM
- Vite
- Git
- GitHub
- VS Code
- Postman

## PDF

The application can use a PDF library such as:

```text
barryvdh/laravel-dompdfs
