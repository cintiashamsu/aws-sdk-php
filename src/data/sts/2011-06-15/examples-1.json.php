<?php
// This file was auto-generated from sdk-root/src/data/sts/2011-06-15/examples-1.json
return [ 'version' => '1.0', 'examples' => [ 'AssumeRole' => [ [ 'input' => [ 'ExternalId' => '123ABC', 'Policy' => '{"Version":"2012-10-17","Statement":[{"Sid":"Stmt1","Effect":"Allow","Action":"s3:ListAllMyBuckets","Resource":"*"}]}', 'RoleArn' => 'arn:aws:iam::123456789012:role/demo', 'RoleSessionName' => 'testAssumeRoleSession', 'Tags' => [ [ 'Key' => 'Project', 'Value' => 'Unicorn', ], [ 'Key' => 'Team', 'Value' => 'Automation', ], [ 'Key' => 'Cost-Center', 'Value' => '12345', ], ], 'TransitiveTagKeys' => [ 'Project', 'Cost-Center', ], ], 'output' => [ 'AssumedRoleUser' => [ 'Arn' => 'arn:aws:sts::123456789012:assumed-role/demo/Bob', 'AssumedRoleId' => 'ARO123EXAMPLE123:Bob', ], 'Credentials' => [ 'AccessKeyId' => 'AKIAIOSFODNN7EXAMPLE', 'Expiration' => '2011-07-15T23:28:33.359Z', 'SecretAccessKey' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYzEXAMPLEKEY', 'SessionToken' => 'AQoDYXdzEPT//////////wEXAMPLEtc764bNrC9SAPBSM22wDOk4x4HIZ8j4FZTwdQWLWsKWHGBuFqwAeMicRXmxfpSPfIeoIYRqTflfKD8YUuwthAx7mSEI/qkPpKPi/kMcGdQrmGdeehM4IC1NtBmUpp2wUE8phUZampKsburEDy0KPkyQDYwT7WZ0wq5VSXDvp75YU9HFvlRd8Tx6q6fE8YQcHNVXAkiY9q6d+xo0rKwT38xVqr7ZD0u0iPPkUL64lIZbqBAz+scqKmlzm8FDrypNC9Yjc8fPOLn9FX9KSYvKTr4rvx3iSIlTJabIQwj2ICCR/oLxBA==', ], 'PackedPolicySize' => 8, ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => '', 'id' => 'to-assume-a-role-1480532402212', 'title' => 'To assume a role', ], ], 'AssumeRoleWithSAML' => [ [ 'input' => [ 'DurationSeconds' => 3600, 'PrincipalArn' => 'arn:aws:iam::123456789012:saml-provider/SAML-test', 'RoleArn' => 'arn:aws:iam::123456789012:role/TestSaml', 'SAMLAssertion' => 'VERYLONGENCODEDASSERTIONEXAMPLExzYW1sOkF1ZGllbmNlPmJsYW5rPC9zYW1sOkF1ZGllbmNlPjwvc2FtbDpBdWRpZW5jZVJlc3RyaWN0aW9uPjwvc2FtbDpDb25kaXRpb25zPjxzYW1sOlN1YmplY3Q+PHNhbWw6TmFtZUlEIEZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOm5hbWVpZC1mb3JtYXQ6dHJhbnNpZW50Ij5TYW1sRXhhbXBsZTwvc2FtbDpOYW1lSUQ+PHNhbWw6U3ViamVjdENvbmZpcm1hdGlvbiBNZXRob2Q9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDpjbTpiZWFyZXIiPjxzYW1sOlN1YmplY3RDb25maXJtYXRpb25EYXRhIE5vdE9uT3JBZnRlcj0iMjAxOS0xMS0wMVQyMDoyNTowNS4xNDVaIiBSZWNpcGllbnQ9Imh0dHBzOi8vc2lnbmluLmF3cy5hbWF6b24uY29tL3NhbWwiLz48L3NhbWw6U3ViamVjdENvbmZpcm1hdGlvbj48L3NhbWw6U3ViamVjdD48c2FtbDpBdXRoblN0YXRlbWVudCBBdXRoPD94bWwgdmpSZXNwb25zZT4=', ], 'output' => [ 'AssumedRoleUser' => [ 'Arn' => 'arn:aws:sts::123456789012:assumed-role/TestSaml', 'AssumedRoleId' => 'ARO456EXAMPLE789:TestSaml', ], 'Audience' => 'https://signin.aws.amazon.com/saml', 'Credentials' => [ 'AccessKeyId' => 'ASIAV3ZUEFP6EXAMPLE', 'Expiration' => '2019-11-01T20:26:47Z', 'SecretAccessKey' => '8P+SQvWIuLnKhh8d++jpw0nNmQRBZvNEXAMPLEKEY', 'SessionToken' => 'IQoJb3JpZ2luX2VjEOz////////////////////wEXAMPLEtMSJHMEUCIDoKK3JH9uGQE1z0sINr5M4jk+Na8KHDcCYRVjJCZEvOAiEA3OvJGtw1EcViOleS2vhs8VdCKFJQWPQrmGdeehM4IC1NtBmUpp2wUE8phUZampKsburEDy0KPkyQDYwT7WZ0wq5VSXDvp75YU9HFvlRd8Tx6q6fE8YQcHNVXAkiY9q6d+xo0rKwT38xVqr7ZD0u0iPPkUL64lIZbqBAz+scqKmlzm8FDrypNC9Yjc8fPOLn9FX9KSYvKTr4rvx3iSIlTJabIQwj2ICCR/oLxBA==', ], 'Issuer' => 'https://integ.example.com/idp/shibboleth', 'NameQualifier' => 'SbdGOnUkh1i4+EXAMPLExL/jEvs=', 'PackedPolicySize' => 6, 'Subject' => 'SamlExample', 'SubjectType' => 'transient', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => '', 'id' => 'to-assume-role-with-saml-14882749597814', 'title' => 'To assume a role using a SAML assertion', ], ], 'AssumeRoleWithWebIdentity' => [ [ 'input' => [ 'DurationSeconds' => 3600, 'Policy' => '{"Version":"2012-10-17","Statement":[{"Sid":"Stmt1","Effect":"Allow","Action":"s3:ListAllMyBuckets","Resource":"*"}]}', 'ProviderId' => 'www.amazon.com', 'RoleArn' => 'arn:aws:iam::123456789012:role/FederatedWebIdentityRole', 'RoleSessionName' => 'app1', 'WebIdentityToken' => 'Atza%7CIQEBLjAsAhRFiXuWpUXuRvQ9PZL3GMFcYevydwIUFAHZwXZXXXXXXXXJnrulxKDHwy87oGKPznh0D6bEQZTSCzyoCtL_8S07pLpr0zMbn6w1lfVZKNTBdDansFBmtGnIsIapjI6xKR02Yc_2bQ8LZbUXSGm6Ry6_BG7PrtLZtj_dfCTj92xNGed-CrKqjG7nPBjNIL016GGvuS5gSvPRUxWES3VYfm1wl7WTI7jn-Pcb6M-buCgHhFOzTQxod27L9CqnOLio7N3gZAGpsp6n1-AJBOCJckcyXe2c6uD0srOJeZlKUm2eTDVMf8IehDVI0r1QOnTV6KzzAI3OY87Vd_cVMQ', ], 'output' => [ 'AssumedRoleUser' => [ 'Arn' => 'arn:aws:sts::123456789012:assumed-role/FederatedWebIdentityRole/app1', 'AssumedRoleId' => 'AROACLKWSDQRAOEXAMPLE:app1', ], 'Audience' => 'client.5498841531868486423.1548@apps.example.com', 'Credentials' => [ 'AccessKeyId' => 'AKIAIOSFODNN7EXAMPLE', 'Expiration' => '2014-10-24T23:00:23Z', 'SecretAccessKey' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYzEXAMPLEKEY', 'SessionToken' => 'AQoDYXdzEE0a8ANXXXXXXXXNO1ewxE5TijQyp+IEXAMPLE', ], 'PackedPolicySize' => 123, 'Provider' => 'www.amazon.com', 'SubjectFromWebIdentityToken' => 'amzn1.account.AF6RHO7KZU5XRVQJGXK6HEXAMPLE', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => '', 'id' => 'to-assume-a-role-as-an-openid-connect-federated-user-1480533445696', 'title' => 'To assume a role as an OpenID Connect-federated user', ], ], 'AssumeRoot' => [ [ 'input' => [ 'DurationSeconds' => 900, 'TargetPrincipal' => '111122223333', 'TaskPolicyArn' => [ 'arn' => 'arn:aws:iam::aws:policy/root-task/S3UnlockBucketPolicy', ], ], 'output' => [ 'Credentials' => [ 'AccessKeyId' => 'ASIAJEXAMPLEXEG2JICEA', 'Expiration' => '2024-11-15T00:05:07Z', 'SecretAccessKey' => '9drTJvcXLB89EXAMPLELB8923FB892xMFI', 'SessionToken' => 'AQoXdzELDDY//////////wEaoAK1wvxJY12r2IrDFT2IvAzTCn3zHoZ7YNtpiQLF0MqZye/qwjzP2iEXAMPLEbw/m3hsj8VBTkPORGvr9jM5sgP+w9IZWZnU+LWhmg+a5fDi2oTGUYcdg9uexQ4mtCHIHfi4citgqZTgco40Yqr4lIlo4V2b2Dyauk0eYFNebHtYlFVgAUj+7Indz3LU0aTWk1WKIjHmmMCIoTkyYp/k7kUG7moeEYKSitwQIi6Gjn+nyzM+PtoA3685ixzv0R7i5rjQi0YE0lf1oeie3bDiNHncmzosRM6SFiPzSvp6h/32xQuZsjcypmwsPSDtTPYcs0+YN/8BRi2/IcrxSpnWEXAMPLEXSDFTAQAM6Dl9zR0tXoybnlrZIwMLlMi1Kcgo5OytwU=', ], 'SourceIdentity' => 'Alice', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => 'The following command retrieves a set of short-term credentials you can use to unlock an S3 bucket for a member account by removing the bucket policy.', 'id' => 'to-launch-a-privileged-session-1731335424565', 'title' => 'To launch a privileged session', ], ], 'DecodeAuthorizationMessage' => [ [ 'input' => [ 'EncodedMessage' => '<encoded-message>', ], 'output' => [ 'DecodedMessage' => '{"allowed": "false","explicitDeny": "false","matchedStatements": "","failures": "","context": {"principal": {"id": "AIDACKCEVSQ6C2EXAMPLE","name": "Bob","arn": "arn:aws:iam::123456789012:user/Bob"},"action": "ec2:StopInstances","resource": "arn:aws:ec2:us-east-1:123456789012:instance/i-dd01c9bd","conditions": [{"item": {"key": "ec2:Tenancy","values": ["default"]},{"item": {"key": "ec2:ResourceTag/elasticbeanstalk:environment-name","values": ["Default-Environment"]}},(Additional items ...)]}}', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => '', 'id' => 'to-decode-information-about-an-authorization-status-of-a-request-1480533854499', 'title' => 'To decode information about an authorization status of a request', ], ], 'GetCallerIdentity' => [ [ 'input' => [], 'output' => [ 'Account' => '123456789012', 'Arn' => 'arn:aws:iam::123456789012:user/Alice', 'UserId' => 'AKIAI44QH8DHBEXAMPLE', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => 'This example shows a request and response made with the credentials for a user named Alice in the AWS account 123456789012.', 'id' => 'to-get-details-about-a-calling-iam-user-1480540050376', 'title' => 'To get details about a calling IAM user', ], [ 'input' => [], 'output' => [ 'Account' => '123456789012', 'Arn' => 'arn:aws:sts::123456789012:assumed-role/my-role-name/my-role-session-name', 'UserId' => 'AKIAI44QH8DHBEXAMPLE:my-role-session-name', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => 'This example shows a request and response made with temporary credentials created by AssumeRole. The name of the assumed role is my-role-name, and the RoleSessionName is set to my-role-session-name.', 'id' => 'to-get-details-about-a-calling-user-federated-with-assumerole-1480540158545', 'title' => 'To get details about a calling user federated with AssumeRole', ], [ 'input' => [], 'output' => [ 'Account' => '123456789012', 'Arn' => 'arn:aws:sts::123456789012:federated-user/my-federated-user-name', 'UserId' => '123456789012:my-federated-user-name', ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => 'This example shows a request and response made with temporary credentials created by using GetFederationToken. The Name parameter is set to my-federated-user-name.', 'id' => 'to-get-details-about-a-calling-user-federated-with-getfederationtoken-1480540231316', 'title' => 'To get details about a calling user federated with GetFederationToken', ], ], 'GetFederationToken' => [ [ 'input' => [ 'DurationSeconds' => 3600, 'Name' => 'testFedUserSession', 'Policy' => '{"Version":"2012-10-17","Statement":[{"Sid":"Stmt1","Effect":"Allow","Action":"s3:ListAllMyBuckets","Resource":"*"}]}', 'Tags' => [ [ 'Key' => 'Project', 'Value' => 'Pegasus', ], [ 'Key' => 'Cost-Center', 'Value' => '98765', ], ], ], 'output' => [ 'Credentials' => [ 'AccessKeyId' => 'AKIAIOSFODNN7EXAMPLE', 'Expiration' => '2011-07-15T23:28:33.359Z', 'SecretAccessKey' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYzEXAMPLEKEY', 'SessionToken' => 'AQoDYXdzEPT//////////wEXAMPLEtc764bNrC9SAPBSM22wDOk4x4HIZ8j4FZTwdQWLWsKWHGBuFqwAeMicRXmxfpSPfIeoIYRqTflfKD8YUuwthAx7mSEI/qkPpKPi/kMcGdQrmGdeehM4IC1NtBmUpp2wUE8phUZampKsburEDy0KPkyQDYwT7WZ0wq5VSXDvp75YU9HFvlRd8Tx6q6fE8YQcHNVXAkiY9q6d+xo0rKwT38xVqr7ZD0u0iPPkUL64lIZbqBAz+scqKmlzm8FDrypNC9Yjc8fPOLn9FX9KSYvKTr4rvx3iSIlTJabIQwj2ICCR/oLxBA==', ], 'FederatedUser' => [ 'Arn' => 'arn:aws:sts::123456789012:federated-user/Bob', 'FederatedUserId' => '123456789012:Bob', ], 'PackedPolicySize' => 8, ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => '', 'id' => 'to-get-temporary-credentials-for-a-role-by-using-getfederationtoken-1480540749900', 'title' => 'To get temporary credentials for a role by using GetFederationToken', ], ], 'GetSessionToken' => [ [ 'input' => [ 'DurationSeconds' => 3600, 'SerialNumber' => 'YourMFASerialNumber', 'TokenCode' => '123456', ], 'output' => [ 'Credentials' => [ 'AccessKeyId' => 'AKIAIOSFODNN7EXAMPLE', 'Expiration' => '2011-07-11T19:55:29.611Z', 'SecretAccessKey' => 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYzEXAMPLEKEY', 'SessionToken' => 'AQoEXAMPLEH4aoAH0gNCAPyJxz4BlCFFxWNE1OPTgk5TthT+FvwqnKwRcOIfrRh3c/LTo6UDdyJwOOvEVPvLXCrrrUtdnniCEXAMPLE/IvU1dYUg2RVAJBanLiHb4IgRmpRV3zrkuWJOgQs8IZZaIv2BXIa2R4OlgkBN9bkUDNCJiBeb/AXlzBBko7b15fjrBs2+cTQtpZ3CYWFXG8C5zqx37wnOE49mRl/+OtkIKGO7fAE', ], ], 'comments' => [ 'input' => [], 'output' => [], ], 'description' => '', 'id' => 'to-get-temporary-credentials-for-an-iam-user-or-an-aws-account-1480540814038', 'title' => 'To get temporary credentials for an IAM user or an AWS account', ], ], ],];
