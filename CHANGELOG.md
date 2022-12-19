### Changelog

### 0.0.17 - 19 Dec 2022
- Fix parsing queries excluding `raw` queries 

### 0.0.16 - 19 Dec 2022
- Allow passing raw queries through `raw` key in `where` filter 

### 0.0.15 - 13 Dec 2022
- Fix responseModel to fullResponse for createEventReservations, createPayments, createHeatMain, createEventReservationLinks
- Fix responseModel to fullResponse for all *Count operations

### 0.0.14 - 7 Sep 2020
- Fix endpoints having composite primary keys 
    - /heatDetails 
        - added: getHeatDetail, updateHeatDetail, deleteHeatDetail
          - modified: getHeatDetails, updateHeatDetails, deleteHeatDetails  
    - /eventHeatDetails 
        - added: getEventHeatDetail, updateEventHeatDetail, deleteEventHeatDetail
        - modified: getEventHeatDetails, updateEventHeatDetails, deleteEventHeatDetails  
    - /memberships 
        - added: getMembership, updateMembership, deleteMembership
        - modified: getMemberships, updateMemberships, deleteMemberships       


### 0.0.13 - 19 Feb 2020
- Remove required parameter from most of the registration fields

### 0.0.12 - 6 Feb 2020
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

