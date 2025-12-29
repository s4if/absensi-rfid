# Security Enhancements for RFID Check-in Endpoint

## Overview

This document describes the security enhancements implemented for the RFID check-in endpoint to prevent spoofing attacks and improve overall security.

## Changes Made

### 1. Changed from GET to POST Method
**File**: `app/Config/Routes.php`

The endpoint has been changed from:
```
GET /rfid/check_in/{device_id}?token={token}&rfid={rfid}
```

To:
```
POST /rfid/check_in/{device_id}
Content-Type: application/x-www-form-urlencoded
Authorization: Bearer {token}

rfid={rfid}
```

**Benefits**:
- Prevents tokens from appearing in server/proxy logs
- URLs not cached or bookmarked
- Follows HTTP/REST best practices for state-changing operations

### 2. Token Moved to Authorization Header
**File**: `app/Controllers/Rfid.php:38-42`

The device token is now read from the `Authorization` header using Bearer authentication format:

```php
$authHeader = $this->request->getHeaderLine('Authorization');
$token = null;
if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    $token = $matches[1];
}
```

**Benefits**:
- Token not exposed in URL
- Follows industry standard for API authentication
- Better separation from application data

### 3. Rate Limiting
**File**: `app/Filters/RateLimit.php`

A new rate limiting filter has been added to prevent brute force attacks:
- **Limit**: 10 requests per IP per 60 seconds
- **Response**: HTTP 429 (Too Many Requests) when limit exceeded
- **Headers**: `X-RateLimit-Limit` and `X-RateLimit-Window` added to responses

**Configuration**:
The filter is registered in `app/Config/Filters.php` and applied to all `/rfid/*` routes.

### 4. IP Whitelist
**File**: `env`

A new environment variable has been added:
```ini
security.ipWhitelist = '192.168.1.100,192.168.1.101'
```

**Validation**: `app/Controllers/Rfid.php:36-44`

When configured, only requests from the whitelisted IP addresses are accepted:
```php
$ipWhitelist = getenv('security.ipWhitelist');
if (!empty($ipWhitelist)) {
    $allowedIps = array_map('trim', explode(',', $ipWhitelist));
    if (!in_array($clientIp, $allowedIps)) {
        log_message('warning', "Unauthorized IP access attempt from {$clientIp}");
        return $this->failForbidden('IP address not authorized');
    }
}
```

**Benefits**:
- Restricts access to known device IPs only
- Prevents attacks from unauthorized networks
- Works in tandem with rate limiting for layered security

### 5. Audit Logging
**File**: `app/Controllers/Rfid.php`

Comprehensive logging has been added to track all RFID check-in attempts:
- **Info level**: Successful attendances
- **Warning level**: Unauthorized access attempts
- **Error level**: Failed operations

**Examples**:
```php
log_message('info', "RFID check-in attempt from device {$device_id} at IP {$clientIp}, RFID: {$rfid}");
log_message('warning', "Unauthorized IP access attempt from {$clientIp} for device {$device_id}");
log_message('warning', "Unknown device access attempt: device_id={$device_id}, IP={$clientIp}");
log_message('warning', "Invalid token attempt: device_id={$device_id}, IP={$clientIp}");
log_message('info', "Student attendance recorded: student_id={$student->id}, session_id={$sess->id}");
```

**Benefits**:
- Complete audit trail for security investigations
- Early detection of suspicious activity
- Ability to identify and block malicious IPs

### 6. Database Audit Logs Table
**Migration**: `2025-12-29-080706_AddAuditLogs.php`

A new table `audit_logs` has been added for persistent audit logging:
- Tracks IP addresses, device IDs, student IDs, session IDs
- Records actions, status (success/failure/warning), and messages
- Indexed for quick queries and analysis

**Table Structure**:
```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    device_id VARCHAR(50),
    student_id INT UNSIGNED,
    session_id INT UNSIGNED,
    action VARCHAR(50),
    status ENUM('success', 'failure', 'warning'),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY (ip_address, created_at),
    KEY (device_id, created_at)
);
```

## Migration Guide

### 1. Update ESP32 Device Code

Update your ESP32 firmware to use the new endpoint format:

```cpp
// Old format (GET)
String url = "http://yourserver.com/rfid/check_in/" + deviceId 
           + "?token=" + token + "&rfid=" + rfidTag;

// New format (POST with Authorization header)
String url = "http://yourserver.com/rfid/check_in/" + deviceId;
http.begin(url);
http.addHeader("Content-Type", "application/x-www-form-urlencoded");
http.addHeader("Authorization", "Bearer " + token);
http.POST("rfid=" + rfidTag);
```

### 2. Configure Environment

Update your `.env` file:
```ini
# Production: Enable IP whitelist
security.ipWhitelist = '192.168.1.100,192.168.1.101,192.168.1.102'

# Development: Leave empty to disable IP whitelist
# security.ipWhitelist = ''
```

### 3. Run Migration

Apply the database migration:
```bash
php spark migrate
```

### 4. Update Web Server

Ensure your web server is configured to:
- Use HTTPS (recommended for production)
- Properly handle POST requests
- Include real client IP addresses in logs

For Apache:
```apache
# Ensure SSL/TLS
<VirtualHost *:443>
    SSLEngine on
    # ...
</VirtualHost>
```

For Nginx:
```nginx
# Real IP handling behind proxy
set_real_ip_from 10.0.0.0/8;
real_ip_header X-Forwarded-For;
```

## Security Best Practices

### 1. Production Deployment

1. **Use HTTPS**: Always use HTTPS in production to encrypt token and data in transit
2. **Enable IP Whitelist**: Configure whitelisted IPs for all RFID devices
3. **Monitor Logs**: Regularly review audit logs for suspicious activity
4. **Rotate Tokens**: Periodically change device tokens (manual process in current implementation)
5. **Update Timezone**: Ensure server timezone matches school timezone

### 2. Device Security

1. **Secure ESP32**: Ensure ESP32 devices are physically secured
2. **Network Isolation**: Place RFID devices on a separate network segment if possible
3. **Firmware Updates**: Keep ESP32 firmware updated with latest security patches
4. **Token Storage**: Store tokens securely in ESP32 memory (consider using encrypted storage)

### 3. Monitoring

1. **Failed Attempts**: Monitor for high numbers of failed authentication attempts
2. **Unusual IPs**: Alert on requests from unknown IP addresses
3. **Rate Limit Hits**: Monitor 429 responses to detect potential attacks
4. **Attendance Patterns**: Review for unusual attendance patterns

### 4. Emergency Response

If you suspect an attack:
1. **Block IP**: Add malicious IP to firewall
2. **Rotate Token**: Change compromised device token in database
3. **Review Logs**: Analyze audit logs for extent of breach
4. **Notify**: Inform relevant parties if student data may be affected

## Testing

### Test the New Endpoint

```bash
# Test with curl
curl -X POST http://yourserver.com/rfid/check_in/DEVICE001 \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "rfid=YOUR_RFID_TAG"
```

### Test Rate Limiting

```bash
# Make 10+ requests quickly - should get 429 on the 11th
for i in {1..12}; do
  curl -X POST http://yourserver.com/rfid/check_in/DEVICE001 \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -H "Authorization: Bearer YOUR_TOKEN" \
    -d "rfid=TEST$i"
  echo "Request $i"
done
```

### Test IP Whitelist

```bash
# Test from unauthorized IP (use different network or VPN)
curl -X POST http://yourserver.com/rfid/check_in/DEVICE001 \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "rfid=TEST"

# Should return: 403 Forbidden
```

## Additional Improvements (Future Work)

While Option 1 provides significant security improvements, consider implementing Option 2 (Comprehensive) for even better security:

1. **HMAC Signing**: Add request signatures using device secrets
2. **Timestamp Validation**: Prevent replay attacks with timestamp windows
3. **Token Rotation**: Implement automatic token rotation
4. **Device Heartbeat**: Monitor device connectivity
5. **Database Token Encryption**: Encrypt tokens at rest in database

## Troubleshooting

### Common Issues

**Issue**: "IP address not authorized"
- **Solution**: Check `security.ipWhitelist` in `.env` and verify device IP

**Issue**: "Too many requests"
- **Solution**: Wait 60 seconds or adjust rate limit in `RateLimit.php`

**Issue**: 404 Not Found
- **Solution**: Ensure routes are updated and cache is cleared: `php spark cache:clear`

**Issue**: Authorization header not reaching PHP
- **Solution**: Check web server configuration for Apache mod_rewrite or Nginx proxy settings

## Support

For questions or issues:
- Review CodeIgniter 4 documentation: https://codeigniter.com/user_guide/
- Check logs: `writable/logs/`
- Enable debug mode in `.env` for detailed error messages
