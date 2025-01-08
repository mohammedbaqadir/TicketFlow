### PR Title

**Format**: `[Type] Brief Description`

#### Guidelines:

- Use the same type as the related issue.
- Start with the issue title and expand on it to clarify the implementation scope if necessary.
    - Example:
        - Issue: `[Feature] Add User Authentication`
        - PR: `[Feature] Add User Authentication with JWT and Session Management`

#### Allowed Types

- **[Bug]**: For fixing a defect in the software.
- **[Feature]**: For adding new functionality or capability.
- **[Enhancement]**: For improving or extending existing features or performance.
- **[Refactor]**: Strictly for internal code changes that do not affect behavior.
- **[Docs]**: For updating or adding documentation.
- **[Chore]**: For maintenance tasks, configuration changes, or small adjustments.

---

### Linked Issue

- **Closes #<issue-number>**

---

### Solution

- What changes are included in this PR?
- Describe the solution, technical details, or key updates.
- Are there any specific design decisions or constraints to highlight?

---

### Changes Made

- List the specific changes made in this PR.
    - Example: "Updated `AuthController` to handle JWT token rotation."
    - Example: "Added a new API endpoint for user registration."

---

### Testing

- How did you test your changes?
    - Example: “Tested locally on Chrome and Firefox.”
    - Example: “Added unit tests for new endpoint.”
- Were any new tests written? If not, why?
- Did you run linting or other checks?