# Voting System Implementation

## Files Created/Modified

### 1. `backend/create/fetchCandidate.php`
- **Purpose**: Fetches candidates from the database
- **Functions**:
  - `getCandidates()` - Gets all candidates
  - `getCandidateElectionBatches()` - Gets unique election batches
  - `getCandidatePositions()` - Gets unique positions

### 2. `backend/cast/cast.php`
- **Purpose**: Handles vote casting with anti-duplicate, anti-revote, anti-change, and anti-delete features
- **Key Functions**:
  - `hasUserVoted($userId)` - Checks if user has already voted
  - `castMultipleVotes($userId, $votes)` - Casts multiple votes at once
  - `getVoteCounts()` - Gets vote counts per candidate
- **Security Features**:
  - Prevents users from voting twice (ANTI-REVOTE)
  - Prevents duplicate voting for same position
  - Updates user's `is_voted` status to 1 after voting
  - Uses database transactions for data integrity

### 3. `backend/api/submitVote.php`
- **Purpose**: API endpoint for submitting votes via AJAX
- **Features**:
  - Validates user session
  - Validates vote data
  - Prevents revoting
  - Returns JSON response

### 4. `public/pages/vote.php`
- **Purpose**: Main voting interface
- **Features**:
  - Dynamically loads candidates from database
  - Groups candidates by position
  - Interactive selection with JavaScript
  - Confirmation before submission
  - AJAX vote submission
  - Disables voting after submission

## Database Structure

### Current `votes` table:
```sql
CREATE TABLE `votes` (
  `vote_id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(7) NOT NULL,
  `cand_id` int NOT NULL,
  `voted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
)
```

### Recommended improvements (optional):
```sql
-- Add these columns for better tracking:
ALTER TABLE `votes` 
ADD COLUMN `election_batch` int NOT NULL DEFAULT 1 AFTER `user_id`,
ADD COLUMN `position` varchar(50) NOT NULL AFTER `cand_id`;

-- Add unique constraint:
ALTER TABLE `votes` 
ADD UNIQUE KEY `unique_user_position` (`user_id`, `election_batch`, `position`);
```

## How to Use

1. **Setup Database**:
   - Import `sql/database_scv.sql` if not already done
   - Run the optional SQL improvements if needed

2. **Access Voting Page**:
   - Navigate to `public/pages/vote.php`
   - User must be logged in (session required)

3. **Voting Process**:
   - Select candidates for each position
   - Review selections in the right panel
   - Click "Submit Vote" to cast votes
   - Once submitted, voting is disabled

4. **Testing**:
   - Test with different users
   - Verify anti-revote feature works
   - Check vote counts in database

## Security Features

1. **Session Validation**: Only logged-in users can vote
2. **Anti-Revote**: Users cannot vote more than once
3. **Data Validation**: All votes are validated before insertion
4. **Transaction Safety**: Uses database transactions for multiple votes
5. **User Status Update**: Sets `is_voted = 1` in users table

## API Endpoint

**URL**: `backend/api/submitVote.php`
**Method**: POST
**Content-Type**: application/json
**Request Body**:
```json
{
  "votes": [
    {"cand_id": 4, "position": "President"},
    {"cand_id": 5, "position": "Vice President"}
  ]
}
```
**Response**:
```json
{
  "success": true,
  "message": "All votes cast successfully!"
}
```

## Troubleshooting

1. **Database Connection Error**:
   - Check `connection/connection.php` credentials
   - Verify database exists and is accessible

2. **Voting Not Working**:
   - Check browser console for JavaScript errors
   - Verify user is logged in (session exists)
   - Check database structure matches expectations

3. **Duplicate Voting Allowed**:
   - Verify `hasUserVoted()` function is working
   - Check if `is_voted` field is being updated

## Future Improvements

1. Add real-time vote counting
2. Add admin dashboard for vote results
3. Add email notifications for vote confirmation
4. Add audit logging for vote tracking
5. Add support for multiple election batches
