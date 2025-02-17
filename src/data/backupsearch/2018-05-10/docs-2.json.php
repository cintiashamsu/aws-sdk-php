<?php
// This file was auto-generated from sdk-root/src/data/backupsearch/2018-05-10/docs-2.json
return [ 'version' => '2.0', 'service' => '<p><fullname>Backup Search</fullname> <p>Backup Search is the recovery point and item level search for Backup.</p> <p>For additional information, see:</p> <ul> <li> <p> <a href="https://docs.aws.amazon.com/aws-backup/latest/devguide/api-reference.html">Backup API Reference</a> </p> </li> <li> <p> <a href="https://docs.aws.amazon.com/aws-backup/latest/devguide/whatisbackup.html">Backup Developer Guide</a> </p> </li> </ul></p>', 'operations' => [ 'GetSearchJob' => '<p>This operation retrieves metadata of a search job, including its progress.</p>', 'GetSearchResultExportJob' => '<p>This operation retrieves the metadata of an export job.</p> <p>An export job is an operation that transmits the results of a search job to a specified S3 bucket in a .csv file.</p> <p>An export job allows you to retain results of a search beyond the search job\'s scheduled retention of 7 days.</p>', 'ListSearchJobBackups' => '<p>This operation returns a list of all backups (recovery points) in a paginated format that were included in the search job.</p> <p>If a search does not display an expected backup in the results, you can call this operation to display each backup included in the search. Any backups that were not included because they have a <code>FAILED</code> status from a permissions issue will be displayed, along with a status message.</p> <p>Only recovery points with a backup index that has a status of <code>ACTIVE</code> will be included in search results. If the index has any other status, its status will be displayed along with a status message.</p>', 'ListSearchJobResults' => '<p>This operation returns a list of a specified search job.</p>', 'ListSearchJobs' => '<p>This operation returns a list of search jobs belonging to an account.</p>', 'ListSearchResultExportJobs' => '<p>This operation exports search results of a search job to a specified destination S3 bucket.</p>', 'ListTagsForResource' => '<p>This operation returns the tags for a resource type.</p>', 'StartSearchJob' => '<p>This operation creates a search job which returns recovery points filtered by SearchScope and items filtered by ItemFilters.</p> <p>You can optionally include ClientToken, EncryptionKeyArn, Name, and/or Tags.</p>', 'StartSearchResultExportJob' => '<p>This operations starts a job to export the results of search job to a designated S3 bucket.</p>', 'StopSearchJob' => '<p>This operations ends a search job.</p> <p>Only a search job with a status of <code>RUNNING</code> can be stopped.</p>', 'TagResource' => '<p>This operation puts tags on the resource you indicate.</p>', 'UntagResource' => '<p>This operation removes tags from the specified resource.</p>', ], 'shapes' => [ 'AccessDeniedException' => [ 'base' => '<p>You do not have sufficient access to perform this action.</p>', 'refs' => [], ], 'BackupCreationTimeFilter' => [ 'base' => '<p>This filters by recovery points within the CreatedAfter and CreatedBefore timestamps.</p>', 'refs' => [ 'SearchScope$BackupResourceCreationTime' => '<p>This is the time a backup resource was created.</p>', ], ], 'ConflictException' => [ 'base' => '<p>This exception occurs when a conflict with a previous successful operation is detected. This generally occurs when the previous operation did not have time to propagate to the host serving the current request.</p> <p>A retry (with appropriate backoff logic) is the recommended response to this exception.</p>', 'refs' => [], ], 'CurrentSearchProgress' => [ 'base' => '<p>This contains information results retrieved from a search job that may not have completed.</p>', 'refs' => [ 'GetSearchJobOutput$CurrentSearchProgress' => '<p>Returns numbers representing BackupsScannedCount, ItemsScanned, and ItemsMatched.</p>', ], ], 'EBSItemFilter' => [ 'base' => '<p>This contains arrays of objects, which may include CreationTimes time condition objects, FilePaths string objects, LastModificationTimes time condition objects, </p>', 'refs' => [ 'EBSItemFilters$member' => NULL, ], ], 'EBSItemFilters' => [ 'base' => NULL, 'refs' => [ 'ItemFilters$EBSItemFilters' => '<p>This array can contain CreationTimes, FilePaths, LastModificationTimes, or Sizes objects.</p>', ], ], 'EBSResultItem' => [ 'base' => '<p>These are the items returned in the results of a search of Amazon EBS backup metadata.</p>', 'refs' => [ 'ResultItem$EBSResultItem' => '<p>These are items returned in the search results of an Amazon EBS search.</p>', ], ], 'EncryptionKeyArn' => [ 'base' => NULL, 'refs' => [ 'GetSearchJobOutput$EncryptionKeyArn' => '<p>The encryption key for the specified search job.</p> <p>Example: <code>arn:aws:kms:us-west-2:111122223333:key/1234abcd-12ab-34cd-56ef-1234567890ab</code>.</p>', 'StartSearchJobInput$EncryptionKeyArn' => '<p>The encryption key for the specified search job.</p>', ], ], 'ExportJobArn' => [ 'base' => NULL, 'refs' => [ 'ExportJobSummary$ExportJobArn' => '<p>This is the unique ARN (Amazon Resource Name) that belongs to the new export job.</p>', 'GetSearchResultExportJobOutput$ExportJobArn' => '<p>The unique Amazon Resource Name (ARN) that uniquely identifies the export job.</p>', 'StartSearchResultExportJobOutput$ExportJobArn' => '<p>This is the unique ARN (Amazon Resource Name) that belongs to the new export job.</p>', ], ], 'ExportJobStatus' => [ 'base' => NULL, 'refs' => [ 'ExportJobSummary$Status' => '<p>The status of the export job is one of the following:</p> <p> <code>CREATED</code>; <code>RUNNING</code>; <code>FAILED</code>; or <code>COMPLETED</code>.</p>', 'GetSearchResultExportJobOutput$Status' => '<p>This is the current status of the export job.</p>', 'ListSearchResultExportJobsInput$Status' => '<p>The search jobs to be included in the export job can be filtered by including this parameter.</p>', ], ], 'ExportJobSummaries' => [ 'base' => NULL, 'refs' => [ 'ListSearchResultExportJobsOutput$ExportJobs' => '<p>The operation returns the included export jobs.</p>', ], ], 'ExportJobSummary' => [ 'base' => '<p>This is the summary of an export job.</p>', 'refs' => [ 'ExportJobSummaries$member' => NULL, ], ], 'ExportSpecification' => [ 'base' => '<p>This contains the export specification object.</p>', 'refs' => [ 'GetSearchResultExportJobOutput$ExportSpecification' => '<p>The export specification consists of the destination S3 bucket to which the search results were exported, along with the destination prefix.</p>', 'StartSearchResultExportJobInput$ExportSpecification' => '<p>This specification contains a required string of the destination bucket; optionally, you can include the destination prefix.</p>', ], ], 'FilePath' => [ 'base' => NULL, 'refs' => [ 'EBSResultItem$FilePath' => '<p>These are one or more items in the results that match values for file paths returned in a search of Amazon EBS backup metadata.</p>', ], ], 'GenericId' => [ 'base' => NULL, 'refs' => [ 'ExportJobSummary$ExportJobIdentifier' => '<p>This is the unique string that identifies a specific export job.</p>', 'GetSearchJobInput$SearchJobIdentifier' => '<p>Required unique string that specifies the search job.</p>', 'GetSearchJobOutput$SearchJobIdentifier' => '<p>The unique string that identifies the specified search job.</p>', 'GetSearchResultExportJobInput$ExportJobIdentifier' => '<p>This is the unique string that identifies a specific export job.</p> <p>Required for this operation.</p>', 'GetSearchResultExportJobOutput$ExportJobIdentifier' => '<p>This is the unique string that identifies the specified export job.</p>', 'ListSearchJobBackupsInput$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', 'ListSearchJobResultsInput$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', 'ListSearchResultExportJobsInput$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', 'SearchJobSummary$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', 'StartSearchJobOutput$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', 'StartSearchResultExportJobInput$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', 'StartSearchResultExportJobOutput$ExportJobIdentifier' => '<p>This is the unique identifier that specifies the new export job.</p>', 'StopSearchJobInput$SearchJobIdentifier' => '<p>The unique string that specifies the search job.</p>', ], ], 'GetSearchJobInput' => [ 'base' => NULL, 'refs' => [], ], 'GetSearchJobOutput' => [ 'base' => NULL, 'refs' => [], ], 'GetSearchResultExportJobInput' => [ 'base' => NULL, 'refs' => [], ], 'GetSearchResultExportJobOutput' => [ 'base' => NULL, 'refs' => [], ], 'IamRoleArn' => [ 'base' => NULL, 'refs' => [ 'StartSearchResultExportJobInput$RoleArn' => '<p>This parameter specifies the role ARN used to start the search results export jobs.</p>', ], ], 'Integer' => [ 'base' => NULL, 'refs' => [ 'CurrentSearchProgress$RecoveryPointsScannedCount' => '<p>This number is the sum of all backups that have been scanned so far during a search job.</p>', 'InternalServerException$retryAfterSeconds' => '<p>Retry the call after number of seconds.</p>', 'SearchScopeSummary$TotalRecoveryPointsToScanCount' => '<p>This is the count of the total number of backups that will be scanned in a search.</p>', 'ThrottlingException$retryAfterSeconds' => '<p>Retry the call after number of seconds.</p>', ], ], 'InternalServerException' => [ 'base' => '<p>An internal server error occurred. Retry your request.</p>', 'refs' => [], ], 'ItemFilters' => [ 'base' => '<p>Item Filters represent all input item properties specified when the search was created.</p> <p>Contains either EBSItemFilters or S3ItemFilters</p>', 'refs' => [ 'GetSearchJobOutput$ItemFilters' => '<p>Item Filters represent all input item properties specified when the search was created.</p>', 'StartSearchJobInput$ItemFilters' => '<p>Item Filters represent all input item properties specified when the search was created.</p> <p>Contains either EBSItemFilters or S3ItemFilters</p>', ], ], 'ListSearchJobBackupsInput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchJobBackupsInputMaxResultsInteger' => [ 'base' => NULL, 'refs' => [ 'ListSearchJobBackupsInput$MaxResults' => '<p>The maximum number of resource list items to be returned.</p>', ], ], 'ListSearchJobBackupsOutput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchJobResultsInput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchJobResultsInputMaxResultsInteger' => [ 'base' => NULL, 'refs' => [ 'ListSearchJobResultsInput$MaxResults' => '<p>The maximum number of resource list items to be returned.</p>', ], ], 'ListSearchJobResultsOutput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchJobsInput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchJobsInputMaxResultsInteger' => [ 'base' => NULL, 'refs' => [ 'ListSearchJobsInput$MaxResults' => '<p>The maximum number of resource list items to be returned.</p>', ], ], 'ListSearchJobsOutput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchResultExportJobsInput' => [ 'base' => NULL, 'refs' => [], ], 'ListSearchResultExportJobsInputMaxResultsInteger' => [ 'base' => NULL, 'refs' => [ 'ListSearchResultExportJobsInput$MaxResults' => '<p>The maximum number of resource list items to be returned.</p>', ], ], 'ListSearchResultExportJobsOutput' => [ 'base' => NULL, 'refs' => [], ], 'ListTagsForResourceRequest' => [ 'base' => NULL, 'refs' => [], ], 'ListTagsForResourceResponse' => [ 'base' => NULL, 'refs' => [], ], 'Long' => [ 'base' => NULL, 'refs' => [ 'CurrentSearchProgress$ItemsScannedCount' => '<p>This number is the sum of all items that have been scanned so far during a search job.</p>', 'CurrentSearchProgress$ItemsMatchedCount' => '<p>This number is the sum of all items that match the item filters in a search job in progress.</p>', 'EBSResultItem$FileSize' => '<p>These are one or more items in the results that match values for file sizes returned in a search of Amazon EBS backup metadata.</p>', 'LongCondition$Value' => '<p>The value of an item included in one of the search item filters.</p>', 'S3ResultItem$ObjectSize' => '<p>These are items in the returned results that match values for object size(s) input during a search of Amazon S3 backup metadata.</p>', 'SearchScopeSummary$TotalItemsToScanCount' => '<p>This is the count of the total number of items that will be scanned in a search.</p>', ], ], 'LongCondition' => [ 'base' => '<p>The long condition contains a <code>Value</code> and can optionally contain an <code>Operator</code>.</p>', 'refs' => [ 'LongConditionList$member' => NULL, ], ], 'LongConditionList' => [ 'base' => NULL, 'refs' => [ 'EBSItemFilter$Sizes' => '<p>You can include 1 to 10 values.</p> <p>If one is included, the results will return only items that match.</p> <p>If more than one is included, the results will return all items that match any of the included values.</p>', 'S3ItemFilter$Sizes' => '<p>You can include 1 to 10 values.</p> <p>If one value is included, the results will return only items that match the value.</p> <p>If more than one value is included, the results will return all items that match any of the values.</p>', ], ], 'LongConditionOperator' => [ 'base' => NULL, 'refs' => [ 'LongCondition$Operator' => '<p>A string that defines what values will be returned.</p> <p>If this is included, avoid combinations of operators that will return all possible values. For example, including both <code>EQUALS_TO</code> and <code>NOT_EQUALS_TO</code> with a value of <code>4</code> will return all values.</p>', ], ], 'ObjectKey' => [ 'base' => NULL, 'refs' => [ 'S3ResultItem$ObjectKey' => '<p>This is one or more items returned in the results of a search of Amazon S3 backup metadata that match the values input for object key.</p>', ], ], 'RecoveryPoint' => [ 'base' => NULL, 'refs' => [ 'RecoveryPointArnList$member' => NULL, ], ], 'RecoveryPointArnList' => [ 'base' => NULL, 'refs' => [ 'SearchScope$BackupResourceArns' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the backup resources.</p>', ], ], 'ResourceArnList' => [ 'base' => NULL, 'refs' => [ 'SearchScope$SourceResourceArns' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the source resources.</p>', ], ], 'ResourceNotFoundException' => [ 'base' => '<p>The resource was not found for this request.</p> <p>Confirm the resource information, such as the ARN or type is correct and exists, then retry the request.</p>', 'refs' => [], ], 'ResourceType' => [ 'base' => NULL, 'refs' => [ 'ResourceTypeList$member' => NULL, 'SearchJobBackupsResult$ResourceType' => '<p>This is the resource type of the search.</p>', ], ], 'ResourceTypeList' => [ 'base' => NULL, 'refs' => [ 'SearchScope$BackupResourceTypes' => '<p>The resource types included in a search.</p> <p>Eligible resource types include S3 and EBS.</p>', ], ], 'ResultItem' => [ 'base' => '<p>This is an object representing the item returned in the results of a search for a specific resource type.</p>', 'refs' => [ 'Results$member' => NULL, ], ], 'Results' => [ 'base' => NULL, 'refs' => [ 'ListSearchJobResultsOutput$Results' => '<p>The results consist of either EBSResultItem or S3ResultItem.</p>', ], ], 'S3ExportSpecification' => [ 'base' => '<p>This specification contains a required string of the destination bucket; optionally, you can include the destination prefix.</p>', 'refs' => [ 'ExportSpecification$s3ExportSpecification' => '<p>This specifies the destination Amazon S3 bucket for the export job. And, if included, it also specifies the destination prefix.</p>', ], ], 'S3ItemFilter' => [ 'base' => '<p>This contains arrays of objects, which may include ObjectKeys, Sizes, CreationTimes, VersionIds, and/or Etags.</p>', 'refs' => [ 'S3ItemFilters$member' => NULL, ], ], 'S3ItemFilters' => [ 'base' => NULL, 'refs' => [ 'ItemFilters$S3ItemFilters' => '<p>This array can contain CreationTimes, ETags, ObjectKeys, Sizes, or VersionIds objects.</p>', ], ], 'S3ResultItem' => [ 'base' => '<p>These are the items returned in the results of a search of Amazon S3 backup metadata.</p>', 'refs' => [ 'ResultItem$S3ResultItem' => '<p>These are items returned in the search results of an Amazon S3 search.</p>', ], ], 'SearchJobArn' => [ 'base' => NULL, 'refs' => [ 'ExportJobSummary$SearchJobArn' => '<p>The unique string that identifies the Amazon Resource Name (ARN) of the specified search job.</p>', 'GetSearchJobOutput$SearchJobArn' => '<p>The unique string that identifies the Amazon Resource Name (ARN) of the specified search job.</p>', 'GetSearchResultExportJobOutput$SearchJobArn' => '<p>The unique string that identifies the Amazon Resource Name (ARN) of the specified search job.</p>', 'SearchJobSummary$SearchJobArn' => '<p>The unique string that identifies the Amazon Resource Name (ARN) of the specified search job.</p>', 'StartSearchJobOutput$SearchJobArn' => '<p>The unique string that identifies the Amazon Resource Name (ARN) of the specified search job.</p>', ], ], 'SearchJobBackupsResult' => [ 'base' => '<p>This contains the information about recovery points returned in results of a search job.</p>', 'refs' => [ 'SearchJobBackupsResults$member' => NULL, ], ], 'SearchJobBackupsResults' => [ 'base' => NULL, 'refs' => [ 'ListSearchJobBackupsOutput$Results' => '<p>The recovery points returned the results of a search job</p>', ], ], 'SearchJobState' => [ 'base' => NULL, 'refs' => [ 'GetSearchJobOutput$Status' => '<p>The current status of the specified search job.</p> <p>A search job may have one of the following statuses: <code>RUNNING</code>; <code>COMPLETED</code>; <code>STOPPED</code>; <code>FAILED</code>; <code>TIMED_OUT</code>; or <code>EXPIRED</code> .</p>', 'ListSearchJobsInput$ByStatus' => '<p>Include this parameter to filter list by search job status.</p>', 'SearchJobBackupsResult$Status' => '<p>This is the status of the search job backup result.</p>', 'SearchJobSummary$Status' => '<p>This is the status of the search job.</p>', ], ], 'SearchJobSummary' => [ 'base' => '<p>This is information pertaining to a search job.</p>', 'refs' => [ 'SearchJobs$member' => NULL, ], ], 'SearchJobs' => [ 'base' => NULL, 'refs' => [ 'ListSearchJobsOutput$SearchJobs' => '<p>The search jobs among the list, with details of the returned search jobs.</p>', ], ], 'SearchScope' => [ 'base' => '<p>The search scope is all backup properties input into a search.</p>', 'refs' => [ 'GetSearchJobOutput$SearchScope' => '<p>The search scope is all backup properties input into a search.</p>', 'StartSearchJobInput$SearchScope' => '<p>This object can contain BackupResourceTypes, BackupResourceArns, BackupResourceCreationTime, BackupResourceTags, and SourceResourceArns to filter the recovery points returned by the search job.</p>', ], ], 'SearchScopeSummary' => [ 'base' => '<p>The summary of the specified search job scope, including: </p> <ul> <li> <p>TotalBackupsToScanCount, the number of recovery points returned by the search.</p> </li> <li> <p>TotalItemsToScanCount, the number of items returned by the search.</p> </li> </ul>', 'refs' => [ 'GetSearchJobOutput$SearchScopeSummary' => '<p>Returned summary of the specified search job scope, including: </p> <ul> <li> <p>TotalBackupsToScanCount, the number of recovery points returned by the search.</p> </li> <li> <p>TotalItemsToScanCount, the number of items returned by the search.</p> </li> </ul>', 'SearchJobSummary$SearchScopeSummary' => '<p>Returned summary of the specified search job scope, including: </p> <ul> <li> <p>TotalBackupsToScanCount, the number of recovery points returned by the search.</p> </li> <li> <p>TotalItemsToScanCount, the number of items returned by the search.</p> </li> </ul>', ], ], 'ServiceQuotaExceededException' => [ 'base' => '<p>The request denied due to exceeding the quota limits permitted.</p>', 'refs' => [], ], 'StartSearchJobInput' => [ 'base' => NULL, 'refs' => [], ], 'StartSearchJobInputNameString' => [ 'base' => NULL, 'refs' => [ 'StartSearchJobInput$Name' => '<p>Include alphanumeric characters to create a name for this search job.</p>', ], ], 'StartSearchJobOutput' => [ 'base' => NULL, 'refs' => [], ], 'StartSearchResultExportJobInput' => [ 'base' => NULL, 'refs' => [], ], 'StartSearchResultExportJobOutput' => [ 'base' => NULL, 'refs' => [], ], 'StopSearchJobInput' => [ 'base' => NULL, 'refs' => [], ], 'StopSearchJobOutput' => [ 'base' => NULL, 'refs' => [], ], 'String' => [ 'base' => NULL, 'refs' => [ 'AccessDeniedException$message' => '<p>User does not have sufficient access to perform this action.</p>', 'ConflictException$message' => '<p>Updating or deleting a resource can cause an inconsistent state.</p>', 'ConflictException$resourceId' => '<p>Identifier of the resource affected.</p>', 'ConflictException$resourceType' => '<p>Type of the resource affected.</p>', 'EBSResultItem$BackupResourceArn' => '<p>These are one or more items in the results that match values for the Amazon Resource Name (ARN) of recovery points returned in a search of Amazon EBS backup metadata.</p>', 'EBSResultItem$SourceResourceArn' => '<p>These are one or more items in the results that match values for the Amazon Resource Name (ARN) of source resources returned in a search of Amazon EBS backup metadata.</p>', 'EBSResultItem$BackupVaultName' => '<p>The name of the backup vault.</p>', 'EBSResultItem$FileSystemIdentifier' => '<p>These are one or more items in the results that match values for file systems returned in a search of Amazon EBS backup metadata.</p>', 'ExportJobSummary$StatusMessage' => '<p>A status message is a string that is returned for an export job.</p> <p>A status message is included for any status other than <code>COMPLETED</code> without issues.</p>', 'GetSearchJobOutput$Name' => '<p>Returned name of the specified search job.</p>', 'GetSearchJobOutput$StatusMessage' => '<p>A status message will be returned for either a earch job with a status of <code>ERRORED</code> or a status of <code>COMPLETED</code> jobs with issues.</p> <p>For example, a message may say that a search contained recovery points unable to be scanned because of a permissions issue.</p>', 'GetSearchResultExportJobOutput$StatusMessage' => '<p>A status message is a string that is returned for search job with a status of <code>FAILED</code>, along with steps to remedy and retry the operation.</p>', 'InternalServerException$message' => '<p>Unexpected error during processing of request.</p>', 'ListSearchJobBackupsInput$NextToken' => '<p>The next item following a partial list of returned backups included in a search job.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchJobBackupsOutput$NextToken' => '<p>The next item following a partial list of returned backups included in a search job.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchJobResultsInput$NextToken' => '<p>The next item following a partial list of returned search job results.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of search job results, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchJobResultsOutput$NextToken' => '<p>The next item following a partial list of search job results.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchJobsInput$NextToken' => '<p>The next item following a partial list of returned search jobs.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchJobsOutput$NextToken' => '<p>The next item following a partial list of returned backups included in a search job.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchResultExportJobsInput$NextToken' => '<p>The next item following a partial list of returned backups included in a search job.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListSearchResultExportJobsOutput$NextToken' => '<p>The next item following a partial list of returned backups included in a search job.</p> <p>For example, if a request is made to return <code>MaxResults</code> number of backups, <code>NextToken</code> allows you to return more items in your list starting at the location pointed to by the next token.</p>', 'ListTagsForResourceRequest$ResourceArn' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the resource.&gt;</p>', 'ResourceArnList$member' => NULL, 'ResourceNotFoundException$message' => '<p>Request references a resource which does not exist.</p>', 'ResourceNotFoundException$resourceId' => '<p>Hypothetical identifier of the resource affected.</p>', 'ResourceNotFoundException$resourceType' => '<p>Hypothetical type of the resource affected.</p>', 'S3ExportSpecification$DestinationBucket' => '<p>This specifies the destination Amazon S3 bucket for the export job.</p>', 'S3ExportSpecification$DestinationPrefix' => '<p>This specifies the prefix for the destination Amazon S3 bucket for the export job.</p>', 'S3ResultItem$BackupResourceArn' => '<p>These are items in the returned results that match recovery point Amazon Resource Names (ARN) input during a search of Amazon S3 backup metadata.</p>', 'S3ResultItem$SourceResourceArn' => '<p>These are items in the returned results that match source Amazon Resource Names (ARN) input during a search of Amazon S3 backup metadata.</p>', 'S3ResultItem$BackupVaultName' => '<p>The name of the backup vault.</p>', 'S3ResultItem$ETag' => '<p>These are one or more items in the returned results that match values for ETags input during a search of Amazon S3 backup metadata.</p>', 'S3ResultItem$VersionId' => '<p>These are one or more items in the returned results that match values for version IDs input during a search of Amazon S3 backup metadata.</p>', 'SearchJobBackupsResult$StatusMessage' => '<p>This is the status message included with the results.</p>', 'SearchJobBackupsResult$BackupResourceArn' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the backup resources.</p>', 'SearchJobBackupsResult$SourceResourceArn' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the source resources.</p>', 'SearchJobSummary$Name' => '<p>This is the name of the search job.</p>', 'SearchJobSummary$StatusMessage' => '<p>A status message will be returned for either a earch job with a status of <code>ERRORED</code> or a status of <code>COMPLETED</code> jobs with issues.</p> <p>For example, a message may say that a search contained recovery points unable to be scanned because of a permissions issue.</p>', 'ServiceQuotaExceededException$message' => '<p>This request was not successful due to a service quota exceeding limits.</p>', 'ServiceQuotaExceededException$resourceId' => '<p>Identifier of the resource.</p>', 'ServiceQuotaExceededException$resourceType' => '<p>Type of resource.</p>', 'ServiceQuotaExceededException$serviceCode' => '<p>This is the code unique to the originating service with the quota.</p>', 'ServiceQuotaExceededException$quotaCode' => '<p>This is the code specific to the quota type.</p>', 'StartSearchJobInput$ClientToken' => '<p>Include this parameter to allow multiple identical calls for idempotency.</p> <p>A client token is valid for 8 hours after the first request that uses it is completed. After this time, any request with the same token is treated as a new request.</p>', 'StartSearchResultExportJobInput$ClientToken' => '<p>Include this parameter to allow multiple identical calls for idempotency.</p> <p>A client token is valid for 8 hours after the first request that uses it is completed. After this time, any request with the same token is treated as a new request.</p>', 'StringCondition$Value' => '<p>The value of the string.</p>', 'TagKeys$member' => NULL, 'TagMap$key' => NULL, 'TagMap$value' => NULL, 'TagResourceRequest$ResourceArn' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the resource.</p> <p>This is the resource that will have the indicated tags.</p>', 'ThrottlingException$message' => '<p>Request was unsuccessful due to request throttling.</p>', 'ThrottlingException$serviceCode' => '<p>This is the code unique to the originating service.</p>', 'ThrottlingException$quotaCode' => '<p>This is the code unique to the originating service with the quota.</p>', 'UntagResourceRequest$ResourceArn' => '<p>The Amazon Resource Name (ARN) that uniquely identifies the resource where you want to remove tags.</p>', 'ValidationException$message' => '<p>The input fails to satisfy the constraints specified by an Amazon service.</p>', ], ], 'StringCondition' => [ 'base' => '<p>This contains the value of the string and can contain one or more operators.</p>', 'refs' => [ 'StringConditionList$member' => NULL, ], ], 'StringConditionList' => [ 'base' => NULL, 'refs' => [ 'EBSItemFilter$FilePaths' => '<p>You can include 1 to 10 values.</p> <p>If one file path is included, the results will return only items that match the file path.</p> <p>If more than one file path is included, the results will return all items that match any of the file paths.</p>', 'S3ItemFilter$ObjectKeys' => '<p>You can include 1 to 10 values.</p> <p>If one value is included, the results will return only items that match the value.</p> <p>If more than one value is included, the results will return all items that match any of the values.</p>', 'S3ItemFilter$VersionIds' => '<p>You can include 1 to 10 values.</p> <p>If one value is included, the results will return only items that match the value.</p> <p>If more than one value is included, the results will return all items that match any of the values.</p>', 'S3ItemFilter$ETags' => '<p>You can include 1 to 10 values.</p> <p>If one value is included, the results will return only items that match the value.</p> <p>If more than one value is included, the results will return all items that match any of the values.</p>', ], ], 'StringConditionOperator' => [ 'base' => NULL, 'refs' => [ 'StringCondition$Operator' => '<p>A string that defines what values will be returned.</p> <p>If this is included, avoid combinations of operators that will return all possible values. For example, including both <code>EQUALS_TO</code> and <code>NOT_EQUALS_TO</code> with a value of <code>4</code> will return all values.</p>', ], ], 'TagKeys' => [ 'base' => NULL, 'refs' => [ 'UntagResourceRequest$TagKeys' => '<p>This required parameter contains the tag keys you want to remove from the source.</p>', ], ], 'TagMap' => [ 'base' => NULL, 'refs' => [ 'ListTagsForResourceResponse$Tags' => '<p>List of tags returned by the operation.</p>', 'SearchScope$BackupResourceTags' => '<p>These are one or more tags on the backup (recovery point).</p>', 'StartSearchJobInput$Tags' => '<p>List of tags returned by the operation.</p>', 'StartSearchResultExportJobInput$Tags' => '<p>Optional tags to include. A tag is a key-value pair you can use to manage, filter, and search for your resources. Allowed characters include UTF-8 letters, numbers, spaces, and the following characters: + - = . _ : /. </p>', 'TagResourceRequest$Tags' => '<p>Required tags to include. A tag is a key-value pair you can use to manage, filter, and search for your resources. Allowed characters include UTF-8 letters, numbers, spaces, and the following characters: + - = . _ : /. </p>', ], ], 'TagResourceRequest' => [ 'base' => NULL, 'refs' => [], ], 'TagResourceResponse' => [ 'base' => NULL, 'refs' => [], ], 'ThrottlingException' => [ 'base' => '<p>The request was denied due to request throttling.</p>', 'refs' => [], ], 'TimeCondition' => [ 'base' => '<p>A time condition denotes a creation time, last modification time, or other time.</p>', 'refs' => [ 'TimeConditionList$member' => NULL, ], ], 'TimeConditionList' => [ 'base' => NULL, 'refs' => [ 'EBSItemFilter$CreationTimes' => '<p>You can include 1 to 10 values.</p> <p>If one is included, the results will return only items that match.</p> <p>If more than one is included, the results will return all items that match any of the included values.</p>', 'EBSItemFilter$LastModificationTimes' => '<p>You can include 1 to 10 values.</p> <p>If one is included, the results will return only items that match.</p> <p>If more than one is included, the results will return all items that match any of the included values.</p>', 'S3ItemFilter$CreationTimes' => '<p>You can include 1 to 10 values.</p> <p>If one value is included, the results will return only items that match the value.</p> <p>If more than one value is included, the results will return all items that match any of the values.</p>', ], ], 'TimeConditionOperator' => [ 'base' => NULL, 'refs' => [ 'TimeCondition$Operator' => '<p>A string that defines what values will be returned.</p> <p>If this is included, avoid combinations of operators that will return all possible values. For example, including both <code>EQUALS_TO</code> and <code>NOT_EQUALS_TO</code> with a value of <code>4</code> will return all values.</p>', ], ], 'Timestamp' => [ 'base' => NULL, 'refs' => [ 'BackupCreationTimeFilter$CreatedAfter' => '<p>This timestamp includes recovery points only created after the specified time.</p>', 'BackupCreationTimeFilter$CreatedBefore' => '<p>This timestamp includes recovery points only created before the specified time.</p>', 'EBSResultItem$CreationTime' => '<p>These are one or more items in the results that match values for creation times returned in a search of Amazon EBS backup metadata.</p>', 'EBSResultItem$LastModifiedTime' => '<p>These are one or more items in the results that match values for Last Modified Time returned in a search of Amazon EBS backup metadata.</p>', 'ExportJobSummary$CreationTime' => '<p>This is a timestamp of the time the export job was created.</p>', 'ExportJobSummary$CompletionTime' => '<p>This is a timestamp of the time the export job compeleted.</p>', 'GetSearchJobOutput$CompletionTime' => '<p>The date and time that a search job completed, in Unix format and Coordinated Universal Time (UTC). The value of <code>CompletionTime</code> is accurate to milliseconds. For example, the value 1516925490.087 represents Friday, January 26, 2018 12:11:30.087 AM.</p>', 'GetSearchJobOutput$CreationTime' => '<p>The date and time that a search job was created, in Unix format and Coordinated Universal Time (UTC). The value of <code>CompletionTime</code> is accurate to milliseconds. For example, the value 1516925490.087 represents Friday, January 26, 2018 12:11:30.087 AM.</p>', 'GetSearchResultExportJobOutput$CreationTime' => '<p>The date and time that an export job was created, in Unix format and Coordinated Universal Time (UTC). The value of <code>CreationTime</code> is accurate to milliseconds. For example, the value 1516925490.087 represents Friday, January 26, 2018 12:11:30.087 AM.</p>', 'GetSearchResultExportJobOutput$CompletionTime' => '<p>The date and time that an export job completed, in Unix format and Coordinated Universal Time (UTC). The value of <code>CreationTime</code> is accurate to milliseconds. For example, the value 1516925490.087 represents Friday, January 26, 2018 12:11:30.087 AM.</p>', 'S3ResultItem$CreationTime' => '<p>These are one or more items in the returned results that match values for item creation time input during a search of Amazon S3 backup metadata.</p>', 'SearchJobBackupsResult$IndexCreationTime' => '<p>This is the creation time of the backup index.</p>', 'SearchJobBackupsResult$BackupCreationTime' => '<p>This is the creation time of the backup (recovery point).</p>', 'SearchJobSummary$CreationTime' => '<p>This is the creation time of the search job.</p>', 'SearchJobSummary$CompletionTime' => '<p>This is the completion time of the search job.</p>', 'StartSearchJobOutput$CreationTime' => '<p>The date and time that a job was created, in Unix format and Coordinated Universal Time (UTC). The value of <code>CompletionTime</code> is accurate to milliseconds. For example, the value 1516925490.087 represents Friday, January 26, 2018 12:11:30.087 AM.</p>', 'TimeCondition$Value' => '<p>This is the timestamp value of the time condition.</p>', ], ], 'UntagResourceRequest' => [ 'base' => NULL, 'refs' => [], ], 'UntagResourceResponse' => [ 'base' => NULL, 'refs' => [], ], 'ValidationException' => [ 'base' => '<p>The input fails to satisfy the constraints specified by a service.</p>', 'refs' => [], ], ],];
