import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Log in as a teacher
        page.goto("http://localhost:8000/teacher/index.php")
        page.get_by_label("Email").fill("john.doe@example.com")
        page.get_by_label("Password").fill("password123")
        page.get_by_role("button", name="Login").click()

        # Wait for navigation to dashboard
        expect(page).to_have_title("Teacher Dashboard")

        # Navigate to the first manage class link
        page.locator('.assignment-card a').first.click()
        expect(page).to_have_title("Manage Class")

        # Click the "Add New Student" button
        page.get_by_role("link", name="Add New Student").click()
        expect(page).to_have_title("Add New Student")

        # Fill out the form
        page.get_by_label("Full Name").fill("Eva Green")
        page.get_by_label("Email").fill("eva.green@example.com")
        page.get_by_label("Address").fill("789 Oak St")
        page.get_by_label("Contact Number").fill("555-4321")
        page.get_by_role("button", name="Add Student").click()

        # After adding, we are redirected to the dashboard.
        # We need to go back to the manage class page to verify.
        expect(page).to_have_title("Teacher Dashboard")
        page.locator('.assignment-card a').first.click()
        expect(page).to_have_title("Manage Class")

        # Verify the new student is in the unassigned list
        expect(page.locator("text=Eva Green")).to_be_visible()

        # Take a screenshot
        page.screenshot(path="jules-scratch/verification/verification.png")
        print("Screenshot saved to jules-scratch/verification/verification.png")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="jules-scratch/verification/error.png")

    finally:
        # Close browser
        context.close()
        browser.close()

with sync_playwright() as playwright:
    run(playwright)