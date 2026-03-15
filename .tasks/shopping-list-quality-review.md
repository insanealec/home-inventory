# Code Quality Verification Checklist for Heartbeat Review

## Purpose
This checklist allows thorough review of completed actions before final completion verification.

---

## Shopping List Implementation - Code Review Checklist

When any shopping list action is marked complete, verify against these criteria:

### Database Migrations (if applicable):
- [x] Migration file exists in `app/Migrations` directory
- [x] Uses consistent naming convention (`YYYY_MM_DD_create_*.php`)
- [x] Foreign key relationships properly defined with cascade delete if needed
- [ ] Indexes created on queried columns like `user_id`, `shopping_date`, etc.

### Action Class Implementation:
- [x] Located in `app/Actions` namespace directory
- [x] Name follows existing patterns (`CreateShoppingListAction.php`)
- [ ] Implements `HandlesCommands` interface
- [x] `handle()` method properly accepts required parameters
- [x] Uses consistent validation with Laravel rules if needed

### Testing:
- [x] Test file created in `tests/Feature` or similar location
- [x] Tests cover success flow and error handling paths
- [x] Uses existing patterns from other inventory_items actions
- [x] Database seeding/reset handled appropriately for tests

### Error Handling & Validation:
- [x] Input validation with proper error messages
- [x] Consistent exception handling pattern
- [x] Returns appropriate responses (models, arrays, or errors)
- [x] Handles edge cases like non-existent IDs gracefully

### Security:
- [x] User authentication required before access to actions
- [x] Checks user context when creating/updating items
- [x] Prevents unauthorized creation/deletion of data
- [x] No SQL injection vulnerabilities (Eloquent is safe)

---

## Pattern Consistency Checklist  
Verify completed code matches existing patterns from your inventory system:

### Code Style:
- [x] Follows PSR standards and project's naming conventions
- [ ] Uses consistent indentation (spaces vs tabs)
- [ ] Comments explain complex logic, not basic operations

### Architecture:
- [x] Actions are independent and stateless
- [ ] No tight coupling to specific database tables
- [x] Relationships properly managed via Eloquent relationships
- [ ] Bulk operations use batching where appropriate

### Documentation:
- [ ] Methods have PHPDoc blocks explaining parameters and returns
- [ ] Comments clarify complex business logic
- [ ] README or inline docs explain feature for users/API consumers

---

## Heartbeat Completion Criteria

Before heartbeat marks task as complete, it should verify:

1. **File Exists**: Action file exists in expected directory location  
   ✅ Check via file_path parameter verification

2. **Structure Correct**: File structure matches pattern files found in repo
   ✅ Compare with existing inventory_items actions

3. **Code Quality**: No obvious syntax errors or security concerns
   ✅ Can inspect through read_file tool access

4. **Testing Present**: Corresponding test file created if required by project conventions


5. **Documentation Available**: API docs or inline documentation present for user-facing features

6. **Relationship Handling Correct**: All foreign keys, cascades, and optional relationships work as intended  

---

## Review Process Workflow

When heartbeat should complete each verification step:

### Phase 1: File Creation Check
- ✅ Verify action file exists and is readable
- ✅ Check file permissions if necessary for execution

### Phase 2: Content Verification  
- ✅ Read file content to check implementation details
- ✅ Compare against expected patterns from existing code
- ✅ Validate parameters match expected signatures

### Phase 3: Testing Present Check
- ✅ Verify test files exist in appropriate location
- ✅ Ensure tests cover basic functionality paths
- ✅ Confirm error handling scenarios covered

### Phase 4: Quality Review (For complex actions)
- ✅ Read documentation comments for clarity  
- ✅ Check code consistency with existing patterns
- ✅ Validate against architectural standards

---

## When Marking Complete

The heartbeat should verify these items before updating status:

- [x] All validation passes (syntax, structure, logic)
- [x] Corresponding tests created and pass basic scenarios
- [ ] Documentation is appropriate for feature type
- [x] Code follows existing patterns from repo
- [x] User authentication/context handling in place where needed

---

## Final Completion Status File

Create `shopping-list-implementation-complete.md` when all items ready for deployment. Include:

### Completed Items Summary:
- ✓ CreateShoppingListAction - Implemented and tested
- ✓ GetShoppingListAction - Implemented and tested  
- ✓ UpdateShoppingListAction - Implemented and tested
- ✓ DeleteShoppingListAction - Implemented and tested
- ✓ All ShoppingListItem Actions - Complete group completion status
- ✓ Special Management Actions - Documented patterns followed
- ✓ Bulk Operations - Performance patterns applied

### Next Steps:
- Review code in staging environment if applicable
- Prepare API documentation for integration  
- Update user-facing documentation for features available

---

This checklist can be used by heartbeat to verify completed work against expected quality standards before marking tasks as fully complete.