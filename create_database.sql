-- Script untuk membuat database inventory_divisi1
-- Jalankan query ini di pgAdmin4 jika database belum ada

CREATE DATABASE inventory_divisi1
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

COMMENT ON DATABASE inventory_divisi1
    IS 'Database untuk sistem inventaris divisi';
