-- Create replication user
CREATE USER 'replicator'@'%' IDENTIFIED BY 'replicatorpass123';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';

-- Create application user
CREATE USER 'laravel'@'%' IDENTIFIED BY 'laravelpass123';
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%';

FLUSH PRIVILEGES;