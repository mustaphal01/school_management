// Function to show/hide sections
function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    document.getElementById(sectionId).style.display = 'block';
}

// Handle student form submission
document.getElementById('studentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const studentData = {
        firstName: document.getElementById('studentFirstName').value,
        lastName: document.getElementById('studentLastName').value,
        email: document.getElementById('studentEmail').value,
        grade: document.getElementById('studentGrade').value
    };

    try {
        const response = await fetch('./php/add_student.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(studentData)
        });

        if (response.ok) {
            alert('Student added successfully!');
            loadStudents();
            e.target.reset();
        } else {
            alert('Error adding student');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding student');
    }
});

// Handle teacher form submission
document.getElementById('teacherForm').addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent the default form submission behavior

    const teacherData = {
        firstName: document.getElementById('teacherFirstName').value,
        lastName: document.getElementById('teacherLastName').value,
        email: document.getElementById('teacherEmail').value,
        subject: document.getElementById('teacherSubject').value
    };

    try {
        const response = await fetch('./php/add_teacher.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(teacherData)
        });

        if (response.ok) {
            alert('Teacher added successfully!');
            loadTeachers(); // Reload the teacher list
            e.target.reset(); // Reset the form
        } else {
            alert('Error adding teacher');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error adding teacher');
    }
});



// Function to load students
async function loadStudents() {
    try {
        const response = await fetch('./php/get_students.php');
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const text = await response.text();
        try {
            const students = JSON.parse(text);

            const studentsList = document.getElementById('studentsList');
            studentsList.innerHTML = '';

            students.forEach(student => {
                const studentElement = document.createElement('div');
                studentElement.className = 'list-item';
                studentElement.innerHTML = `
                    <div>
                        ${student.first_name} ${student.last_name} - ${student.email} (Grade: ${student.grade})
                    </div>
                    <button class="delete-btn" onclick="deleteStudent(${student.id})">Delete</button>
                `;
                studentsList.appendChild(studentElement);
            });
        } catch (jsonError) {
            console.error('Invalid JSON:', text);
            throw jsonError;
        }
    } catch (error) {
        console.error('Error loading students:', error);
    }
}



// Function to delete student
async function deleteStudent(id) {
    if (!confirm('Are you sure you want to delete this student?')) return;
    
    try {
        const response = await fetch(`./php/delete_student.php?id=${id}`);
        if (response.ok) {
            loadStudents();
        } else {
            alert('Error deleting student');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting student');
    }
}

// Teacher functions
async function loadTeachers() {
    try {
        const response = await fetch('./php/get_teachers.php'); // Fetch data from the server
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const teachers = await response.json(); // Parse JSON data

        const teachersList = document.getElementById('teachersList');
        teachersList.innerHTML = ''; // Clear existing teacher list

        teachers.forEach(teacher => {
            const teacherElement = document.createElement('div');
            teacherElement.className = 'list-item';
            teacherElement.innerHTML = `
                <div>
                    ${teacher.first_name} ${teacher.last_name} - ${teacher.email} (Subject: ${teacher.subject})
                </div>
                <button class="delete-btn" onclick="deleteTeacher(${teacher.id})">Delete</button>
            `;
            teachersList.appendChild(teacherElement);
        });
    } catch (error) {
        console.error('Error loading teachers:', error);
    }
}


// Function to load classes
async function loadClasses() {
    try {
        const response = await fetch('./php/get_classes.php'); // Fetch data from the server
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const classes = await response.json(); // Parse JSON data

        const classesList = document.getElementById('classesList');
        classesList.innerHTML = ''; // Clear existing class list

        classes.forEach(classItem => {
            const classElement = document.createElement('div');
            classElement.className = 'list-item';
            classElement.innerHTML = `
                <div>
                    ${classItem.class_name} - Teacher: ${classItem.teacher_name || 'Not Assigned'}
                </div>
                <button class="delete-btn" onclick="deleteClass(${classItem.id})">Delete</button>
            `;
            classesList.appendChild(classElement);
        });
    } catch (error) {
        console.error('Error loading classes:', error);
    }
}


// Load initial data
document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
    loadTeachers();
    loadClasses();
});