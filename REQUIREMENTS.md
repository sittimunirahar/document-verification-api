## Requirements

Build a REST API with Laravel where an authenticated user sends a JSON file and receives a verification result as a response

**Sample JSON file content**

```json
{
  "data": {
    "id": "63c79bd9303530645d1cca00",
    "name": "Certificate of Completion",
    "recipient": {
      "name": "Marty McFly",
      "email": "marty.mcfly@gmail.com"
    },
    "issuer": {
      "name": "Accredify",
      "identityProof": {
        "type": "DNS-DID",
        "key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
        "location": "ropstore.accredify.io"
      }
    },
    "issued": "2022-12-23T00:00:00+08:00"
  },
  "signature": {
    "type": "SHA3MerkleProof",
    "targetHash": "288f94aadadf486cfdad84b9f4305f7d51eac62db18376d48180cc1dd2047a0e"
  }
}
```

**Expected Response Body**

```json
{
  "data": {
    "issuer": "Accredify", // name of the issuer of the file
    "result": "verified" // allowed values are "verified", "invalid_recipient", "invalid_issuer", or "invalid_signature"
  }
}
```

**Conditions for “Verified”**

- A file is considered as “verified” when it fulfils the following three conditions
    
    **Condition 1**: JSON has a valid recipient
    
    - `recipient` must have `name` and `email`
    - error code: `invalid_recipient`
    
    **Condition 2**: JSON has a valid issuer
    
    - `issuer` must have `name` and `identityProof`
    - The value of `issuer.identityProof.key` (i.e. Ethereum wallet address) must be found in the DNS TXT record of the domain name specified by `issuer.identityProof.location`
        - For the sample JSON, `did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller` is found in the DNS TXT record of `ropstore.accredify.io`
        - Can use Google DNS API for DNS lookup
            - https://developers.google.com/speed/public-dns/docs/doh/json
            - e.g. https://dns.google/resolve?name=ropstore.accredify.io&type=TXT
    - error code: `invalid_issuer`
    
    **Condition 3**: JSON has a valid signature
    
    - We ensure the JSON is not tampered with by computing the “target hash” and comparing it with the target hash in the JSON (i.e. `signature.targetHash`).
    The file is considered “unverified” if the two hashes don’t match.
    - A target hash is computed by the following steps
        1. List each property's path from the `data` object using a dot notation, and associate its value
            
            ```json
            {
                "id": "63c79bd9303530645d1cca00",
                "name": "Certificate of Completion",
                "recipient.name": "Marty McFly",
                "recipient.email": "marty.mcfly@gmail.com",
                "issuer.name": "Accredify",
                "issuer.identityProof.type": "DNS-DID",
                "issuer.identityProof.key": "did:ethr:0x05b642ff12a4ae545357d82ba4f786f3aed84214#controller",
                "issuer.identityProof.location": "ropstore.accredify.io",
                "issued": "2022-12-23T00:00:00+08:00"
            }
            ```
            
        2. For each property's path, compute a hash using the property's key-value pair.
        Use `sha256` for the hashing algorithm.
            
            ```json
            [
              "8d79f393cc294fd3daca0402209997db5ff8a2ad1a498702f0956952677881ae",
              "cd77eab0fa4b92136f883dfe6fe63d7ee68a98a7697874609a5f9d24adaa0f04",
              "d94a0e7c2e7f61c7b29fede334c1b501a8b7cc8d46876273e92c4412ad82f575",
              "b38da593123c5295845996b08502a115c2ed5e1f42745ed45fba2a0b4ea3ed47",
              "88e287c3b0e2fcaeac173b7a20e3357342ad75cb2ceb849b3f7176c4026379b2",
              "9cba7ec835e861763731506e3b7712cfcab46ccb735fadd9a4e7c85716972144",
              "39fa9881a7607ee77cfaa82b982f4e809fc96c8ebf4891d98349ba3d71bc1a8e",
              "14ee1b33dd3084a127a6e2e6807fca79f317e2df3a9069c50e5f5adb4da84bb8",
              "a8aa49c6d150fab1fd77213f1f182c42ece261b30822b0c1c12826ef4599238b",
            ]
            ```
            
            e.g. `8d79f393cc294fd3daca0402209997db5ff8a2ad1a498702f0956952677881ae` (the first hash above) is computed from `{"id":"63c79bd9303530645d1cca00"}`
            
        3. Sort all the hashes from the previous step alphabetically and hash them all together using `sha256`. This will provide the target hash of the file.
            
            <aside>
            💡 If you manually try out these steps using some online tools before writing code, make sure to remove whitespace from the array of hashes. It will result in a different hash with whitespace. Don’t worry about whitespace when you hash the array in PHP.
            
            </aside>
            
    - error code: `invalid_signature`

**Assumptions/Expectations**

- The maximum file size is 2MB
- The API should return a `200` status code even if the file is not verified
- Store the verification result in the database for analysis purposes.
    - User ID
    - File type (only supports JSON for now)
    - Verification result
    - Timestamp

## Important Notes

- Ensure the API is scalable so that new verification logic can be easily added with minimal changes to the existing code. The actual verification endpoint has over 10 verification logics.
- Add meaningful tests that meet at least 80% code coverage
- Follow [Accredify’s coding standards](https://www.notion.so/Laravel-PHP-conventions-ffe7c6222c054e9ca94ff7570e89068c?pvs=21). **(We place great importance on the consistency of our codebase)**
- Provide API docs that describe how to consume the endpoint, status codes that the endpoint may return, and the response body of each status code, ideally using OpenAPI.
- `README.md` should contain all the information that the reviewers need to run and test the app
- `ROADMAP.md` with any refactoring or improvement that you would do given you have more time
- Lightweight technical documentation such as diagrams or design considerations
- The chosen libraries and tools may prove your experience using them
- Don’t hesitate to ask questions if any clarification is required
