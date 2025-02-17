<?php
// This file was auto-generated from sdk-root/src/data/ssm/2014-11-06/examples-1.json
return [ 'version' => '1.0', 'examples' => [ 'GetExecutionPreview' => [ [ 'input' => [ 'ExecutionPreviewId' => '2f27d6e5-9676-4708-b8bd-aef0ab47bb26', ], 'output' => [ 'EndedAt' => '2024-11-13T01:50:39.424000+00:00', 'ExecutionPreview' => [ 'Automation' => [ 'Regions' => [ 'us-east-2', ], 'StepPreviews' => [ 'Undetermined' => 1, ], 'TotalAccounts' => 1, ], ], 'ExecutionPreviewId' => '2f27d6e5-9676-4708-b8bd-aef0ab47bb26', 'Status' => 'Success', ], 'description' => 'This example illustrates one usage of GetExecutionPreview', 'id' => 'getexecutionpreview-f6ae6a7e734e', 'title' => 'GetExecutionPreview', ], ], 'ListNodes' => [ [ 'input' => [ 'Filters' => [ [ 'Key' => 'Region', 'Type' => 'Equal', 'Values' => [ 'us-east-2', ], ], ], 'MaxResults' => 1, 'SyncName' => 'AWS-QuickSetup-ManagedNode', ], 'output' => [ 'NextToken' => 'A9lT8CAxj9aDFRi+MNAoFq08IEXAMPLE', 'Nodes' => [ [ 'CaptureTime' => '2024-11-19T22:01:18', 'Id' => 'i-02573cafcfEXAMPLE', 'NodeType' => [ 'Instance' => [ 'AgentType' => 'amazon-ssm-agent', 'AgentVersion' => '3.3.859.0', 'ComputerName' => 'ip-192.0.2.0.ec2.internal', 'InstanceStatus' => 'Active', 'IpAddress' => '192.0.2.0', 'ManagedStatus' => 'Managed', 'PlatformName' => 'Amazon Linux', 'PlatformType' => 'Linux', 'PlatformVersion' => '2023', 'ResourceType' => 'EC2Instance', ], ], 'Owner' => [ 'AccountId' => '111122223333', 'OrganizationalUnitId' => 'ou-b8dn-sasv9tfp', 'OrganizationalUnitPath' => 'r-b8dn/ou-b8dn-sasv9tfp', ], 'Region' => 'us-east-2', ], ], ], 'description' => 'This example illustrates one usage of ListNodes', 'id' => 'listnodes--ec13d561ee02', 'title' => 'ListNodes', ], ], 'ListNodesSummary' => [ [ 'input' => [ 'Aggregators' => [ [ 'AggregatorType' => 'Count', 'AttributeName' => 'Region', 'TypeName' => 'Instance', ], ], 'Filters' => [ [ 'Key' => 'InstanceStatus', 'Type' => 'Equal', 'Values' => [ 'Active', ], ], ], 'MaxResults' => 2, 'NextToken' => 'A9lT8CAxj9aDFRi+MNAoFq08I---EXAMPLE', 'SyncName' => 'AWS-QuickSetup-ManagedNode', ], 'output' => [ 'Summary' => [ [ 'Count' => '26', 'Region' => 'us-east-1', ], [ 'Count' => '7', 'Region' => 'us-east-2', ], ], ], 'description' => 'This example illustrates one usage of ListNodesSummary', 'id' => 'listnodessummary-9a63f9e71ee0', 'title' => 'ListNodesSummary', ], ], 'StartExecutionPreview' => [ [ 'input' => [ 'DocumentName' => 'AWS-StartEC2Instance', ], 'output' => [ 'ExecutionPreviewId' => '2f27d6e5-9676-4708-b8bd-aef0ab47bb26', ], 'description' => 'This example illustrates one usage of StartExecutionPreview', 'id' => 'startexecutionpreview-7a6b962646a9', 'title' => 'StartExecutionPreview', ], ], ],];
