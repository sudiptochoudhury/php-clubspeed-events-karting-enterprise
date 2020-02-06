### Changelog

### 0.0.12 - 6 Feb 2019
- Change response for createCheckDetails method

### 0.0.11 - 1 July 2019
- Add new Response Model ([status:integer, body:\GuzzleHttp\Psr7\Stream]) for string responses

### 0.0.10 - 23 May 2019
- `heatId` is not available for `finalize` when purchasing gift cards.

### 0.0.9
- Make `sendCustomerReceiptEmail` of `finalizeCheck` optional

### 0.0.8
- Added `getGiftCardsHistory` endpoint  

### 0.0.7
- Easier way to set `base_uri` e.g., `new Api(['api_key' => 'xxxx', 'base_uri' => 'http://...']);`  

### 0.0.6
- Modify finalizeCheck and createPayments requests

### 0.0.5
- Disabled log by default

### 0.0.4
- Fix issue with missing operator in for BETWEEN  

### 0.0.3
- Allow additional query parameters in GET methods
- Add static functions to generate select, filter, order, limit, skip parameters for GET methods
- Cleanup

### 0.0.2
- Changes names

### 0.0.1
- Initial release 

