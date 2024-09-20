# Changelog

All notable changes to `filament-failed-jobs` will be documented in this file.

## Made queue column toggleable - 2024-09-20

When queues are processes by the aws sqs, fomr Vapor for example, the queue name is a url which is quite long to be shown in the table. So ability to hide queue column was added.

## Retry filtered - 2024-09-14

Added button to retry filtered failed jobs, this button shows up in the table header only when at least one filter is used.

## First version - 2024-09-13

Filament plugin for managing failed jobs offers a streamlined interface to monitor, retry, and delete failed jobs directly from the admin panel.

## 1.0.0 - 202X-XX-XX

- initial release
