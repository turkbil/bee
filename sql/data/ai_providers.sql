-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: 127.0.0.1    Database: laravel
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `ai_providers`
--

LOCK TABLES `ai_providers` WRITE;
/*!40000 ALTER TABLE `ai_providers` DISABLE KEYS */;
INSERT INTO `ai_providers` (`id`, `name`, `display_name`, `service_class`, `default_model`, `available_models`, `default_settings`, `api_key`, `base_url`, `is_active`, `is_default`, `priority`, `average_response_time`, `description`, `token_cost_multiplier`, `tokens_per_request_estimate`, `cost_structure`, `tracks_usage`, `credit_cost_multiplier`, `credits_per_request_estimate`, `created_at`, `updated_at`) VALUES (1,'deepseek','DeepSeek','DeepSeekService','deepseek-chat','{\"deepseek-chat\": {\"name\": \"DeepSeek Chat\", \"input_cost\": 0.07, \"output_cost\": 0.27}, \"deepseek-reasoner\": {\"name\": \"DeepSeek Reasoner\", \"input_cost\": 0.14, \"output_cost\": 0.95}}','{\"top_p\": 0.9, \"max_tokens\": 4000, \"temperature\": 0.7}','eyJpdiI6IjBGQXV4emErK3NtUEJRcXg0ZHRnV2c9PSIsInZhbHVlIjoiUFZOUFVyWDdCaW9LMWtLY25zaGZvN091Z0hCajlrWFZkMWNmRFVWdlpCR3paR0ZYUEtQK0t1RStJSWdxbFlSKyIsIm1hYyI6IjgwYzQzYjNhMTk3N2M0YWY5OGIyODY3MjFmYjJkNDQ5MWY4MjBhODFhMThhZmVlM2UwZTZhYTk1MTczODI2NzgiLCJ0YWciOiIifQ==','https://api.deepseek.com',1,0,30,0.00,'DeepSeek AI - Yüksek performanslı AI modeli (Fallback)',1.0000,1000,'{\"chat\": {\"input\": 0.07, \"output\": 0.27}, \"reasoning\": {\"input\": 0.14, \"output\": 0.95}}',1,0.5000,120,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_providers` (`id`, `name`, `display_name`, `service_class`, `default_model`, `available_models`, `default_settings`, `api_key`, `base_url`, `is_active`, `is_default`, `priority`, `average_response_time`, `description`, `token_cost_multiplier`, `tokens_per_request_estimate`, `cost_structure`, `tracks_usage`, `credit_cost_multiplier`, `credits_per_request_estimate`, `created_at`, `updated_at`) VALUES (2,'openai','OpenAI GPT','OpenAIService','gpt-4o','{\"gpt-4o\": {\"name\": \"GPT-4o\", \"input_cost\": 2.5, \"output_cost\": 10}, \"gpt-4o-mini\": {\"name\": \"GPT-4o Mini\", \"input_cost\": 0.15, \"output_cost\": 0.6}, \"gpt-3.5-turbo\": {\"name\": \"GPT-3.5 Turbo\", \"input_cost\": 0.5, \"output_cost\": 1.5}}','{\"top_p\": 0.9, \"max_tokens\": 4000, \"temperature\": 0.7}','eyJpdiI6IlhEWXBPNUs0RERSWmhsanNkRnZSbGc9PSIsInZhbHVlIjoiNi9ON2p0Vm94bTFlU1lSazlhUjhuTzc3V1hJTnRoOEV6NlF2OWl6cWxwZStNam5oQS9WYnJoVWVUeTYwZWJsR3V1dXc3WS9pZlNyTXoxRzNCZVlrQWRTWHh2T0hieHZGOHVNbDVjS1BDblpBdDl4SEZiQkZxNEU0QkIrbi9xQXErWUVNRHhBcGpJZ000QStEaWpaeEhkRjV5OUMrUHN1eTFNbDIwSVZpLzVrYUNpM1Y3b1lxMVdMdHpwcDZqZDB6NXgwbENWaWNmdXA4SWszalhwcWVYaUxHSEQrZHNqMVVwTnU5dGNBNXpSUT0iLCJtYWMiOiI1OTFmNTQxNWU4ZmFjZWEwY2IzZDJmMDRjYzU4MGFlYmQ3MGRjNDI4NDBiZjBkNGM3Yjc4YzdmMTU2NTNmOTg4IiwidGFnIjoiIn0=','https://api.openai.com/v1',1,1,10,0.00,'OpenAI GPT modelleri - Güçlü dil modeli',1.0000,1000,'{\"gpt-4o\": {\"input\": 2.5, \"output\": 10}, \"gpt-4o-mini\": {\"input\": 0.15, \"output\": 0.6}, \"gpt-3.5-turbo\": {\"input\": 0.5, \"output\": 1.5}}',1,1.0000,100,'2025-10-05 00:43:13','2025-10-05 00:43:13');
INSERT INTO `ai_providers` (`id`, `name`, `display_name`, `service_class`, `default_model`, `available_models`, `default_settings`, `api_key`, `base_url`, `is_active`, `is_default`, `priority`, `average_response_time`, `description`, `token_cost_multiplier`, `tokens_per_request_estimate`, `cost_structure`, `tracks_usage`, `credit_cost_multiplier`, `credits_per_request_estimate`, `created_at`, `updated_at`) VALUES (3,'anthropic','Anthropic Claude','AnthropicService','claude-3-haiku-20240307','{\"claude-3-haiku-20240307\": {\"name\": \"Claude 3 Haiku\", \"input_cost\": 0.25, \"output_cost\": 1.25}, \"claude-3-5-sonnet-20241022\": {\"name\": \"Claude 3.5 Sonnet\", \"input_cost\": 3, \"output_cost\": 15}}','{\"max_tokens\": 4000, \"temperature\": 0.7}','eyJpdiI6IjNmbnBmSHBLUGY5TDUyVUdEUVhvdFE9PSIsInZhbHVlIjoiY0Q4QVVESzl3ZkJGL1NvM1lqSytWaVdoOVdkcTd5SS95NVJMTkxNdklGbnhWTHpsRHlXUEZMcW9RMGppeE1jdWtmUmFjZE5taDNWL0gwRmx3WEV2NWF4UGxFMllwR0dDZ0c0N0NVelY3Z2N4V1dySi9CZmdMUzZ4R1VKaDV1Y3dTdlNYdTBUbnlOYmd6UEd4cDhTdDRBPT0iLCJtYWMiOiI3ZjE1NDhlYmRkNGFhNzlhYWRjMjI3MmVmZjBhZmE3YzdjMzBhZDY4ZjEyNmVmNzEwNjIxNjI1NWVlNzVlNDlmIiwidGFnIjoiIn0=','https://api.anthropic.com',1,0,20,0.00,'Anthropic Claude - Güvenli ve akıllı AI asistan',1.0000,1000,'{\"claude-3-haiku\": {\"input\": 0.25, \"output\": 1.25}, \"claude-3.5-sonnet\": {\"input\": 3, \"output\": 15}}',1,1.2000,90,'2025-10-05 00:43:13','2025-10-05 00:43:13');
/*!40000 ALTER TABLE `ai_providers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-05 16:14:16
